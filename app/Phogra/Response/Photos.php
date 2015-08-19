<?php

namespace App\Phogra\Response;

use App\Phogra\Response\Item\Photo;

class Photos extends BaseResponse
{

	public function __construct($data) {
		parent::__construct();

		if (is_array($data)) {
			$this->data = [];
			$current = null;
			foreach ($data as $row) {
				if (!isset($current) || $row->id != $current->id) {
					$current = new Photo($row);
					$this->data[] = $current;
				}

				if (isset($row->file_id)) {
					$current->addFile($row);
				}
			}
		} else {
			$this->data = new Photo($data);
			if (isset($data->file_id)) {
				$this->data->addFile($data);
			}
		}
	}
}