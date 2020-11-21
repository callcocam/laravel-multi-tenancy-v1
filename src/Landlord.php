<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant;

use Call\Tenant\Models\Tenant;

class Landlord
{
    public static function execute(callable $callable)
    {
        $originalCurrentTenant = Tenant::current();

        Tenant::forgetCurrent();

        $result = $callable();

        optional($originalCurrentTenant)->makeCurrent();

        return $result;
    }
}
