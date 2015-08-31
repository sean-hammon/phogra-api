<?php

namespace App\Phogra\Response\Item;

class Gallery extends ResponseItem
{
	public function __construct($row) {

        parent::__construct();

		$this->type = 'galleries';

		$this->id = $row->id;
		$this->attributes = (object)[
			'parent_id' => $row->parent_id,
			'title' => $row->title,
			'slug' => $row->slug,
			'description' => $row->description,
			'protected' => $row->protected,
			'created_at' => $row->created_at,
			'updated_at' => $row->updated_at
		];

		$this->relationships = (object)[
			"children" => (object)[
				"type"  => "galleries",
				"data" => ($row->children == null ? null : explode(',', $row->children)),
				"links" => (object)[
					"self" => $this->baseUrl . "/galleries/{$row->id}/children"
				]
			],
			"photos" => (object)[
				"type"  => "photos",
				"data" => ($row->photos == null ? null : explode(',', $row->photos)),
				"links" => (object)[
					"self" => $this->baseUrl . "/galleries/{$row->id}/photos"
				]
			]
		];

		if ($row->children != null) {
			$this->relationships->children->links->related = $this->baseUrl . "/galleries/{$row->children}";
		}
		if ($row->photos != null) {
			$this->relationships->photos->links->related = $this->baseUrl . "/photos/{$row->photos}";
		}

		$this->links = (object)[
			"self" => $this->baseUrl . "/galleries/{$row->id}"
		];
	}

}