<?php

namespace App\Phogra\Response\Item;

use Hashids;

class Gallery extends ResponseItem
{
	private $childHashes = [];
    private $childIds = [];
	private $photoHashes = [];
    private $photoIds = [];

	public function __construct($row) {

        parent::__construct();

		$this->type = 'galleries';

		$this->id = Hashids::encode($row->id);
		$this->attributes = (object)[
			'parent_id' => Hashids::encode($row->parent_id),
			'title' => $row->title,
			'slug' => $row->slug,
			'node' => $row->node,
			'description' => $row->description,
			'protected' => $row->protected,
			'created_at' => $row->created_at,
			'updated_at' => $row->updated_at
		];

		if ($row->children != null) {
			$this->childIds = explode(',', $row->children);
			$this->childHashes = array_map("Hashids::encode", $this->childIds);
		}
		if ($row->photos != null) {
			$this->photoIds = explode(',', $row->photos);
			$this->photoHashes = array_map("Hashids::encode", $this->photoIds);
		}

		$this->relationships = (object)[
			"children" => (object)[
				"type"  => "galleries",
				"data" => ($row->children == null ? null : $this->childHashes),
				"links" => (object)[
					"self" => $this->baseUrl . "/galleries/{$this->id}/children"
				]
			],
			"photos" => (object)[
				"type"  => "photos",
				"data" => ($row->photos == null ? null : $this->photoHashes),
				"links" => (object)[
					"self" => $this->baseUrl . "/galleries/{$this->id}/photos"
				]
			]
		];

		if (count($this->childHashes) > 0) {
			$this->relationships->children->links->related = $this->baseUrl . "/galleries/" . Hashids::encode($this->childIds);
		}

		if (count($this->photoHashes) > 0) {
			$this->relationships->photos->links->related = $this->baseUrl . "/photos/" . Hashids::encode($this->photoIds);
		}

		$this->links = (object)[
			"self" => $this->baseUrl . "/galleries/{$this->id}"
		];
	}

}