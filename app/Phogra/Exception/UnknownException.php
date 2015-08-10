<?php

namespace App\Phogra\Exception;

class UnknownException extends PhograException
{
	public function __construct($message = "Unexpect server error.", $code = 500, \Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}