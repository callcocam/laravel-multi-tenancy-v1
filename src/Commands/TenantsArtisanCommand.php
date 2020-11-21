<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Call\Tenant\Commands\Concerns\TenantAware;
use Call\Tenant\Models\Tenant;

class TenantsArtisanCommand extends Command
{
    use  TenantAware;

    protected $signature = 'tenants:artisan {artisanCommand} {--tenant=*}';

    public function handle()
    {
        if (! $artisanCommand = $this->argument('artisanCommand')) {
            $artisanCommand = $this->ask('Which artisan command do you want to run for all tenants?');
        }

        $tenant = Tenant::current();

        $this->line('');
        $this->info("Running command for tenant `{$tenant->name}` (id: {$tenant->getKey()})...");
        $this->line("---------------------------------------------------------");
        Artisan::call($artisanCommand, [], $this->output);
    }
}
