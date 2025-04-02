<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct(
        string $message = 'Error',
        int $code = 400,

    ) {
        parent::__construct($message, $code);
    }
}
