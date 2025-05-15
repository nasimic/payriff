<?php

namespace Nasimic\Payriff\Exceptions;

use Exception;

class EmptySecretKeyException extends Exception implements \Throwable
{
    public function __construct()
    {
        parent::__construct('Secret key is not provided by configuration');
    }
}