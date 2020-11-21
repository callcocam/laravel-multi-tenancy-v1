<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Concerns;

use Illuminate\Support\Arr;
use Call\Tenant\Exceptions\InvalidConfiguration;

trait UsesMultitenancyConfig
{
    public function tenantDatabaseConnectionName(): ?string
    {
          return config('multitenancy.tenant_database_connection_name');
    }

    public function landlordDatabaseConnectionName(): ?string
    {

        return config('multitenancy.landlord_database_connection_name');
    }

    public function currentTenantContainerKey(): string
    {
        return config('multitenancy.current_tenant_container_key');
    }

    public function getMultitenancyActionClass(string $actionName, string $actionClass)
    {
        $configuredClass = config("multitenancy.actions.{$actionName}");
        if (is_null($configuredClass)) {
            $configuredClass = $actionClass;
        }

        if (! is_a($configuredClass, $actionClass, true)) {
            throw InvalidConfiguration::invalidAction($actionName, $configuredClass ?? '', $actionClass);
        }

        return app($configuredClass);
    }

    public function getTenantArtisanSearchFields() : array
    {
        return Arr::wrap(config('multitenancy.tenant_artisan_search_fields'));
    }
}
