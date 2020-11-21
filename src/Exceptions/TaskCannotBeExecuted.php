<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Exceptions;

use Exception;
use Call\Tenant\Tasks\SwitchTenantTask;

class TaskCannotBeExecuted extends Exception
{
    public static function make(SwitchTenantTask $task, string $reason): self
    {
        $taskClass = get_class($task);

        return new static("Task `{$taskClass}` could not be executed. Reason: {$reason}");
    }
}
