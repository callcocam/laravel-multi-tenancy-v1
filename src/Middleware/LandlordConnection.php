<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Middleware;

use Call\Tenant\Models\LandlordUser;
use Closure;
use Illuminate\Http\Request;

class LandlordConnection
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
            'auth.providers.users.model' => config('landlord_model_user', LandlordUser::class),
        ]);
        return $next($request);
    }
}
