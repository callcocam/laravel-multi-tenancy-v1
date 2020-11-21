<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Tasks;

use Illuminate\Support\Facades\DB;
use Call\Tenant\Concerns\UsesMultitenancyConfig;
use Call\Tenant\Exceptions\InvalidConfiguration;
use Call\Tenant\Models\Tenant;

class SwitchTenantDatabaseTask implements SwitchTenantTask
{
    use UsesMultitenancyConfig;

    public function makeCurrent(Tenant $tenant): void
    {
        $this->setTenantConnectionDatabaseName($tenant->getDatabaseName());
    }

    public function forgetCurrent(): void
    {
        $this->setTenantConnectionDatabaseName(null);
    }

    protected function setTenantConnectionDatabaseName(?string $databaseName)
    {
        $tenantConnectionName = $this->tenantDatabaseConnectionName();

        if ($tenantConnectionName === $this->landlordDatabaseConnectionName()) {
            throw InvalidConfiguration::tenantConnectionIsEmptyOrEqualsToLandlordConnection();
        }

        if (is_null(config("database.connections.{$tenantConnectionName}"))) {
            throw InvalidConfiguration::tenantConnectionDoesNotExist($tenantConnectionName);
        }

        config([
            "database.connections.{$tenantConnectionName}.database" => $databaseName,
        ]);

        DB::purge($tenantConnectionName);
    }
}
