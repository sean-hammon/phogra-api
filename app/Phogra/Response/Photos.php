<?php

namespace App\Phogra\Response;

class Photos extends BaseResponse
{

	public function __construct($rows) {
		parent::__construct();
		$this->data = [];

		foreach ($rows as $row) {
			$this->data[] = new Photo($row);
		}

	}
}