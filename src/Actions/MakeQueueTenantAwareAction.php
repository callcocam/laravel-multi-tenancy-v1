<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Actions;

use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Queue\Events\JobProcessing;
use Call\Tenant\Exceptions\CurrentTenantCouldNotBeDeterminedInTenantAwareJob;
use Call\Tenant\Jobs\NotTenantAware;
use Call\Tenant\Jobs\TenantAware;
use Call\Tenant\Models\Concerns\UsesTenantModel;
use Call\Tenant\Models\Tenant;

class MakeQueueTenantAwareAction
{
    use UsesTenantModel;

    public function execute()
    {
        $this
            ->listenForJobsBeingQueued()
            ->listenForJobsBeingProcessed();
    }

    protected function listenForJobsBeingQueued()
    {
        app('queue')->createPayloadUsing(function ($connectionName, $queue, $payload) {
            $queueable = $payload['data']['command'];

            if (!$this->isTenantAware($queueable)) {
                return [];
            }

            return ['tenantId' => optional(Tenant::current())->id];
        });

        return $this;
    }

    protected function listenForJobsBeingProcessed()
    {
        app('events')->listen(JobProcessing::class, function (JobProcessing $event) {
            if (!array_key_exists('tenantId', $event->job->payload())) {
                return;
            }

            $this->findTenant($event)->makeCurrent();
        });

        return $this;
    }

    protected function isTenantAware(object $queueable)
    {
        $reflection = new \ReflectionClass($this->getJobFromQueueable($queueable));

        if ($reflection->implementsInterface(TenantAware::class)) {
            return true;
        } elseif ($reflection->implementsInterface(NotTenantAware::class)) {
            return false;
        }

        return config('multitenancy.queues_are_tenant_aware_by_default') === true;
    }

    protected function findTenant(JobProcessing $event)
    {
        $tenantId = $event->job->payload()['tenantId'];

        if (!$tenantId) {
            $event->job->delete();

            throw CurrentTenantCouldNotBeDeterminedInTenantAwareJob::noIdSet($event);
        }


        /** @var \Call\Tenant\Models\Tenant $tenant */
        if (!$tenant = $this->getTenantModel()::find($tenantId)) {
            $event->job->delete();

            throw CurrentTenantCouldNotBeDeterminedInTenantAwareJob::noTenantFound($event);
        }

        return $tenant;
    }

    protected function getJobFromQueueable(object $queueable)
    {
        switch (get_class($queueable)) {
            case SendQueuedMailable::class:
                return $queueable->mailable;
            case SendQueuedNotifications::class:
                return $queueable->notification;
            case CallQueuedListener::class:
                return $queueable->class;
            case BroadcastEvent::class:
                return $queueable->event;
            default:
                return $queueable;
        }
    }
}
