<?php

namespace App\Phogra\Response;

use \DateTime;
use Hashids;
use App\Phogra\Response\Item\Photo;

class Image extends BaseResponse
{

	public function __construct($data) {
		parent::__construct();

		$this->data = new File($data);
		$this->lastModified = new DateTime($data->updated_at);

		$this->etag = md5(json_encode($this->data));
	}
}