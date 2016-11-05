<?php
/**
 * User: Sean
 * Date: 11/4/2016
 * Time: 8:26 PM
 */

namespace App\Phogra\Response;

use App\Phogra\Response\Item\Tag;

class Tags extends BaseResponse
{

	public function __construct($data)
	{
		parent::__construct();

		if (is_array($data)) {
			$this->data = [];
			foreach ($data as $row) {
				$this->data[] = new Tag($row);
			}
		} else {
			$this->data = new Tag($data);
		}

	}
}