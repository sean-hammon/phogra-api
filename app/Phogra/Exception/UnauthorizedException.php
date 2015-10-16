<?php

namespace App\Phogra\Exception;

class UnauthorizedException extends PhograException
{
    public function __construct($message = "Unauthorized request.", $code = 401, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}