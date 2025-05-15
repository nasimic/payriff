<?php

namespace Nasimic\Payriff\Exceptions;

use Exception;

class CurrencyNotAllowedException extends Exception implements \Throwable
{

    public function __construct()
    {
        parent::__construct('Currency not allowed. Allowed currencies are: AZN, USD and EUR');
    }
}
