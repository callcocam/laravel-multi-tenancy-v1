<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Models\Concerns;

use Call\Tenant\Concerns\UsesMultitenancyConfig;

trait UsesLandlordConnection
{
    use UsesMultitenancyConfig;

    public function getConnectionName()
    {

        return $this->landlordDatabaseConnectionName();
    }
}
