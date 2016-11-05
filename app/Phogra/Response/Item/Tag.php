<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sean
 * Date: 11/4/2016
 * Time: 8:28 PM
 */

namespace App\Phogra\Response\Item;

use Hashids;

class Tag extends ResponseItem
{

	public function __construct($row)
	{

		parent::__construct();

		$this->type = 'tags';

		$this->id = Hashids::encode($row->id);
		$this->attributes = (object)[
			'name' => $row->name
		];

		$this->links = (object)[
			"self" => $this->baseUrl . "/tag/{$this->id}"
		];
	}
}