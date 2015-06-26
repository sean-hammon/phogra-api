<?php

namespace App\Phogra\Response;

class Galleries extends BaseResponse
{

	public function __construct($rows) {
		$this->data = [];

		foreach ($rows as $row) {
			$this->data[] = new Gallery($row);
		}

	}
}