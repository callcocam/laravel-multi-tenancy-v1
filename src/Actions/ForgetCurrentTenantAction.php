<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Actions;

use Call\Tenant\Events\ForgettingCurrentTenantEvent;
use Call\Tenant\Events\ForgotCurrentTenantEvent;
use Call\Tenant\Models\Tenant;
use Call\Tenant\Tasks\SwitchTenantTask;
use Call\Tenant\Tasks\TasksCollection;

class ForgetCurrentTenantAction
{
    private $tasksCollection;

    public function __construct(TasksCollection $tasksCollection)
    {
        $this->tasksCollection = $tasksCollection;
    }

    public function execute(Tenant $tenant)
    {
        event(new ForgettingCurrentTenantEvent($tenant));

        $this
            ->performTaskToForgetCurrentTenant()
            ->clearBoundCurrentTenant();

        event(new ForgotCurrentTenantEvent($tenant));
    }

    protected function performTaskToForgetCurrentTenant(): self
    {
        $this->tasksCollection->each(function(SwitchTenantTask $task) {
            return $task->forgetCurrent();
        });

        return $this;
    }

    private function clearBoundCurrentTenant()
    {
        $containerKey = config('multitenancy.current_tenant_container_key');

        app()->forgetInstance($containerKey);
    }
}
