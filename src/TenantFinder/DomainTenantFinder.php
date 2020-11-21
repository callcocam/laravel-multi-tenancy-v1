<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\TenantFinder;

use Illuminate\Http\Request;
use Call\Tenant\Models\Concerns\UsesTenantModel;
use Call\Tenant\Models\Tenant;

class DomainTenantFinder extends TenantFinder
{
    use UsesTenantModel;

    public function findForRequest(Request $request): ?Tenant
    {
        $host = str_replace('www.','', $request->getHost());

        $tenant = $this->getTenantModel()::whereDomain($host)->first();

        if($tenant){
            $tenant->append('tenant_id');
        }

        return $tenant;
    }
}
