<?php

namespace App\Phogra\Response;

class Galleries extends BaseResponse
{

	public function __construct($data) {
		parent::__construct();
		$this->allowedHttpVerbs = 'GET, HEAD, OPTIONS, POST, PUT, DELETE';

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