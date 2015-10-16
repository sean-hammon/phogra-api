<?php

namespace App\Phogra\Exception;

class NotFoundException extends PhograException
{
    public function __construct($message, $code = 404, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}