<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant;

use Call\Tenant\Traits\BelongsToTenants;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Ramsey\Uuid\Uuid;

class TenantManager
{
    use Macroable;

    /**
     * Applies applicable tenant scopes to a model.
     *
     * @param Model|BelongsToTenants $model
     */
    public function applyTenantScopes(Model $model)
    {
        if (!$model->isTenant()) {
            return;
        }

        if (!app()->has('currentTenant')) {
             return;
        }
        $this->modelTenants($model)->each(function ($id, $tenant) use ($model) {
            $model->addGlobalScope($tenant, function (Builder $builder) use ($tenant, $id, $model) {
                  $builder->where($model->getQualifiedTenant($tenant), '=', $id);
            });
        });
    }

    /**
     * Add tenant columns as needed to a new model instance before it is created.
     *
     * @param Model $model
     */
    public function newModel(Model $model)
    {
        if (!$model->isTenant()) {
            return;
        }

        if (!app()->has('currentTenant')) {
            return;
        }
        $this->modelTenants($model)->each(function ($tenantId, $tenantColumn) use ($model) {
            $model->setAttribute('id', Uuid::uuid4());
            if (!isset($model->{$tenantColumn})) {
                $model->setAttribute($tenantColumn, $tenantId);
            }
        });
    }

    /**
     * Get a new Eloquent Builder instance without any of the tenant scopes applied.
     *
     * @param Model $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQueryWithoutTenants(Model $model)
    {
        return $model->newQuery()->withoutGlobalScopes(app('currentTenant')->keys()->toArray());
    }

    /**
     * Get the tenantColumns that are actually applicable to the given
     * model, in case they've been manually specified.
     *
     * @param Model|BelongsToTenants $model
     *
     * @return Collection
     */
    protected function modelTenants(Model $model)
    {
        return collect(app('currentTenant')->only($model->getTenantColumns()));
    }
}
