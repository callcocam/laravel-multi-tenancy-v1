<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Tasks;

use Call\Tenant\Models\Concerns\UsesTenantModel;

class LoadTenantsTask
{

    use UsesTenantModel;

    public static function make()
    {

        $tenant = new static();

        return $tenant->execute();
    }

    protected function execute()
    {
        $tenantQuery = $this->getTenantModel()::query()->get();

        return $tenantQuery;
    }
}
