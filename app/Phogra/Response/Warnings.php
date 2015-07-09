<?php

namespace App\Phogra\Response;

class Warnings
{
	private $warnings;

	public function __construct() {
		$this->warnings = [];
	}

	public function addWarning($warning) {
		$this->warnings[] = $warning;
	}

	public function getWarnings() {
		return $this->warnings;
	}

}