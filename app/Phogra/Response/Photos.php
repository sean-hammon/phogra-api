<?php

namespace App\Phogra\Response;

class Photos extends BaseResponse
{

	public function __construct($data) {
		parent::__construct();

		if (is_array($data)) {
			$this->data = [];
			foreach ($data as $row) {
				$this->data[] = new Photo($row);
			}
		} else {
			$this->data = new Photo($data);
		}


	}
}