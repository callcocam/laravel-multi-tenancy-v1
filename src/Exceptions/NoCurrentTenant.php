<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Exceptions;

use Exception;

class NoCurrentTenant extends Exception
{
    public static function make()
    {
        return new static('The request expected a current tenant but none was set.');
    }
}
