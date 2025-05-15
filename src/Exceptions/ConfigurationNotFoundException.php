<?php

namespace Nasimic\Payriff\Exceptions;

use Exception;

class ConfigurationNotFoundException extends Exception implements \Throwable
{
    public function __construct()
    {
        parent::__construct('Configuration not found. Please run "php artisan vendor:publish --tag=payriff-config"');
    }
}