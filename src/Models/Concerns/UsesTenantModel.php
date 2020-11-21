<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Models\Concerns;

use Call\Tenant\Models\Tenant;

trait UsesTenantModel
{
    public function getTenantModel()
    {
        $tenantModelClass = config('multitenancy.tenant_model');

        return new $tenantModelClass;
    }
}
