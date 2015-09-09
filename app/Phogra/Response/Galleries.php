<?php

namespace App\Phogra\Response;

use \DateTime;
use App\Phogra\Response\Item\Gallery;

class Galleries extends BaseResponse
{

	public function __construct($data) {
		parent::__construct();
		$this->allowedHttpVerbs = 'GET, HEAD, OPTIONS, POST, PUT, DELETE';

		if (is_array($data)) {
			$this->data = [];
			foreach ($data as $row) {
				$this->data[] = new Gallery($row);
				$updated = new DateTime($row->updated_at);
				if ($updated > $this->lastModified) {
					$this->lastModified = $updated;
				}
			}
		} else {
			$this->data = new Gallery($data);
			$this->lastModified = new DateTime($data->updated_at);
		}

		$this->etag = md5(json_encode($this->data));

	}
}