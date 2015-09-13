<?php

namespace App\Phogra\Response;

use \DateTime;
use Hashids;
use App\Phogra\Response\Item\Photo;

class Files extends BaseResponse
{

	public function __construct($data) {
		parent::__construct();

		if (is_array($data)) {
			$this->data = [];
			$current = null;
			foreach ($data as $row) {
				$current = new File($row);
				$this->data[] = $current;
				$updated = new DateTime($row->updated_at);
				if ($updated > $this->lastModified) {
					$this->lastModified = $updated;
				}
			}
		} else {
			$this->data = new File($data);
			$this->lastModified = new DateTime($data->updated_at);
		}

		$this->etag = md5(json_encode($this->data));
	}
}