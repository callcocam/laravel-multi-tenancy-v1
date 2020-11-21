<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant;

use Illuminate\Database\Eloquent\Collection;
use Call\Tenant\Models\Tenant;

class TenantCollection extends Collection
{
    public function eachCurrent(callable $callable)
    {
        return $this->performCollectionMethodWhileMakingTenantsCurrent('each', $callable);
    }

    public function mapCurrent(callable $callable)
    {
        return $this->performCollectionMethodWhileMakingTenantsCurrent('map', $callable);
    }

    protected function performCollectionMethodWhileMakingTenantsCurrent(string $operation, callable $callable): self
    {
        $collection = $this->$operation(function(Tenant $tenant) use ($callable){
          return  $tenant->execute($callable);
        });

        return new static($collection->items);
    }
}
