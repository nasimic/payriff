<?php

namespace Nasimic\Payriff\Exceptions;

use Exception;

class LanguageNotAllowedException extends Exception implements \Throwable
{

    public function __construct()
    {
        parent::__construct('Language not allowed. Allowed languages are: AZ, EN and RU');
    }
}