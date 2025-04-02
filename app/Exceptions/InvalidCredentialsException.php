<?php

namespace App\Exceptions;


use Exception;

class InvalidCredentialsException extends Exception
{

    public function __construct(
        string $message = 'Gredenciales inválidas',
        int $code = 401,
    ) {
        parent::__construct($message, $code);
    }
}
