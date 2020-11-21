<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Middleware;

use Call\Models\Landlord;
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
            'auth.providers.users.model' => Landlord::class,
        ]);
        return $next($request);
    }
}
