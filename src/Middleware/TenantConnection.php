<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Middleware;

use Call\Tenant\Models\TenantUser;
use Closure;
use Illuminate\Http\Request;

class TenantConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        config([
            'auth.providers.users.model' => config('tenants_model_user', TenantUser::class),
        ]);
        return $next($request);
    }
}
