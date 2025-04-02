<?php

namespace App\Exceptions;

use Exception;

class AuthException extends Exception
{
    public function __construct(
        string $message = 'Error de autenticación',
        int $code = 401,
        
    ) {
        parent::__construct($message, $code);
    }
}
