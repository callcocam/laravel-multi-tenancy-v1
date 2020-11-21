<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\TenantFinder;

use Illuminate\Http\Request;
use Call\Tenant\Models\Tenant;

abstract class TenantFinder
{
    abstract public function findForRequest(Request $request): ?Tenant;
}
