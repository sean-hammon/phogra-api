<?php

namespace App\Phogra\Exception;

class DuplicateFileException extends PhograException
{
    public function __construct($message, $code = 400, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}