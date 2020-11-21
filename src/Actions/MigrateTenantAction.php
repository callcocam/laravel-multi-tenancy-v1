<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Actions;

use Illuminate\Support\Facades\Artisan;
use Call\Tenant\Models\Tenant;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateTenantAction
{
    protected $fresh = false;

    protected  $seed = false;

    protected  $output;

    public function fresh(bool $fresh = true): self
    {
        $this->fresh = $fresh;

        return $this;
    }

    public function seed(bool $seed = true): self
    {
        $this->seed = $seed;

        return $this;
    }

    public function output(OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function execute(Tenant $tenant): self
    {
        $previousTenant = Tenant::current();

        $tenant->makeCurrent();

        $migrationCommand = $this->fresh ? 'migrate:fresh' : 'migrate';

        Artisan::call($migrationCommand, $this->getOptions(), $this->output);

        optional($previousTenant)->makeCurrent();

        return $this;
    }

    protected function getOptions(): array
    {
        $options = ['--force' => true];

        if ($this->seed) {
            $options['--seed'] = true;
        }

        return $options;
    }
}
