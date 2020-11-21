<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Middleware;

use Closure;
use Call\Tenant\Exceptions\NoCurrentTenant;
use Call\Tenant\Models\Concerns\UsesTenantModel;

class NeedsTenant
{
    use UsesTenantModel;

    public function handle($request, Closure $next)
    {

        if (! $this->getTenantModel()::checkCurrent()) {
            return $this->handleInvalidRequest();
        }
        return $next($request);
    }

    public function handleInvalidRequest(): void
    {
        throw NoCurrentTenant::make();
    }
}
