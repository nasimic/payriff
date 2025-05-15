<?php

namespace Nasimic\Payriff\Exceptions;

use Exception;

class OperationNotAllowedException extends Exception implements \Throwable
{

    public function __construct()
    {
        parent::__construct('Operation not allowed. Allowed operations are: PURCHASE and PRE_AUTH');
    }
}
