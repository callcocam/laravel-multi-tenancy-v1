<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Actions;

use Call\Tenant\Events\MadeTenantCurrentEvent;
use Call\Tenant\Events\MakingTenantCurrentEvent;
use Call\Tenant\Models\Tenant;
use Call\Tenant\Tasks\SwitchTenantTask;
use Call\Tenant\Tasks\TasksCollection;

class MakeTenantCurrentAction
{
    protected $tasksCollection;

    public function __construct(TasksCollection $tasksCollection)
    {
        $this->tasksCollection = $tasksCollection;
    }

    public function execute(Tenant $tenant)
    {
        event(new MakingTenantCurrentEvent($tenant));

        $this
            ->performTasksToMakeTenantCurrent($tenant)
            ->bindAsCurrentTenant($tenant);

        event(new MadeTenantCurrentEvent($tenant));

        return $this;
    }

    protected function performTasksToMakeTenantCurrent(Tenant $tenant): self
    {
        $this->tasksCollection->each(function(SwitchTenantTask $task) use ($tenant){
            return $task->makeCurrent($tenant);
        });

        return $this;
    }

    protected function bindAsCurrentTenant(Tenant $tenant): self
    {
        $containerKey = config('multitenancy.current_tenant_container_key');

        app()->forgetInstance($containerKey);

        app()->instance($containerKey, $tenant);

        return $this;
    }
}
