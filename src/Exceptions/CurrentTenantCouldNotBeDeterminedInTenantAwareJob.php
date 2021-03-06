<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Exceptions;

use Exception;
use Illuminate\Queue\Events\JobProcessing;

class CurrentTenantCouldNotBeDeterminedInTenantAwareJob extends Exception
{
    public static function noIdSet(JobProcessing $event)
    {
        return new static("The current tenant could not be determined in a job named `" . $event->job->getName() . "`. No `tenantId` was set in the payload.");
    }

    public static function noTenantFound(JobProcessing $event): self
    {
        return new static("The current tenant could not be determined in a job named `" . $event->job->getName() . "`. The tenant finder could not find a tenant.");
    }
}
