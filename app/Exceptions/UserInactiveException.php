<?php

namespace App\Exceptions;

use Exception;

class UserInactiveException extends Exception
{
    public function __construct(
        string $message = 'El usuario está inactivo',
        int $code = 403,
    ) {
        parent::__construct($message, $code);
    }
}
