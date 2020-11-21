<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Commands\Concerns;

use Illuminate\Support\Arr;
use Call\Tenant\Concerns\UsesMultitenancyConfig;
use Call\Tenant\Models\Concerns\UsesTenantModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait TenantAware
{
    protected $currentTenants = [];
    use UsesMultitenancyConfig, UsesTenantModel;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       //$tenants = Arr::wrap($this->option('tenant'));
        $tenants = Arr::wrap('tenants');
        $tenantQuery = $this->getTenantModel()::query()
            ->when(!blank($tenants), function ($query) use ($tenants) {
                collect($this->getTenantArtisanSearchFields())->unique('database')
                    ->each(function($field) use ($query, $tenants){
                        return $query->orWhereIn($field, Arr::wrap($tenants));
                    });
            });
        if ($tenantQuery->count() === 0) {
            $this->error('No tenant(s) found.');

            return -1;
        }

        return $tenantQuery
            ->cursor()
            ->map(function ($tenant) {
                if (!in_array($tenant->database, $this->currentTenants)) {
                    $tenant->execute(function() {
                        return (int)$this->laravel->call([$this, 'handle']);
                    });
                }
                $this->currentTenants[$tenant->database] = $tenant->database;
            })
            ->sum();
    }
}
