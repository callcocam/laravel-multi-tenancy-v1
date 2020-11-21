<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Tasks;

use Call\Tenant\Models\Tenant;

interface SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void;

    public function forgetCurrent(): void;
}
