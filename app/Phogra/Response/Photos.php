<?php

namespace App\Phogra\Response;

use \DateTime;
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
				$updated = new DateTime($row->updated_at);
				if ($updated > $this->lastModified) {
					$this->lastModified = $updated;
				}

				if (isset($row->file_id)) {
					$current->addFile($row);
				}
			}
		} else {
			$this->data = new Photo($data);
			$this->lastModified = new DateTime($data->updated_at);
			if (isset($data->file_id)) {
				$this->data->addFile($data);
			}
		}

		$this->etag = md5(json_encode($this->data));
	}
}