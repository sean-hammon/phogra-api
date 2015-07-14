<?php

namespace App\Phogra\Response;

class Galleries extends BaseResponse
{

	public function __construct($data) {
		parent::__construct();

		if (is_array($data)) {
			$this->data = [];
			foreach ($data as $row) {
				$this->data[] = new Gallery($row);
			}
		} else {
			$this->data = new Gallery($data);
		}

	}
}