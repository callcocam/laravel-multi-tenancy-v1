<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Tasks;

use Call\Tenant\Models\Tenant;

class PrefixCacheTask implements SwitchTenantTask
{
    protected $originalPrefix;

    private $storeName;

    private $cacheKeyBase;

    public function __construct(string $storeName = null, string $cacheKeyBase = null)
    {
        $this->originalPrefix = config('cache.prefix');

        $this->storeName = $storeName ?? config('cache.default');

        $this->cacheKeyBase = $cacheKeyBase ?? 'tenant_id_';
    }

    public function makeCurrent(Tenant $tenant): void
    {
        $this->setCachePrefix($this->cacheKeyBase . $tenant->id);
    }

    public function forgetCurrent(): void
    {
        $this->setCachePrefix($this->originalPrefix);
    }

    protected function setCachePrefix(string $prefix)
    {
        config()->set('cache.prefix', $prefix);

        app('cache')->forgetDriver($this->storeName);
    }
}
