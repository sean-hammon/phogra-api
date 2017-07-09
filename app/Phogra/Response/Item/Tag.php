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
	private $photoHashes = [];
	private $photoIds    = [];


	public function __construct($row)
	{

		parent::__construct();

		$this->type = 'tags';

		$this->id = Hashids::encode($row->id);
		$this->attributes = (object)[
			'name' => $row->name
		];

		$this->links = (object)[
			"self" => $this->baseUrl . "/tag/" . urlencode($this->attributes->name)
		];

		if ($row->photos != null) {
			$this->photoIds = explode(',', $row->photos);
			$this->photoHashes = array_map("Hashids::encode", $this->photoIds);
		}

		$this->relationships = (object)[
			"photos" => (object)[
				"type" => "photos",
				"data" => ($row->photos == null ? null : $this->photoHashes),
				"links" => (object)[
					"self" => $this->baseUrl . "/galleries/{$this->id}/photos"
				]
			]
		];

		if (count($this->photoHashes) > 0) {
			$this->relationships->photos->links->related = $this->baseUrl . "/photos/" . Hashids::encode($this->photoIds);
		}
	}
}