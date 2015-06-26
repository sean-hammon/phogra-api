<?php

namespace App\Phogra\Response;

class Gallery
{
	var $type = 'galleries';
	var $id = null;
	var $attributes;
	var $relationships;
	var $links;

	public function __construct($row) {
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
					"self" => "/galleries/{$row->id}/children"
				]
			],
			"photos" => (object)[
				"type"  => "photos",
				"data" => ($row->photos == null ? null : explode(',', $row->photos)),
				"links" => (object)[
					"self" =>  "/galleries/{$row->id}/photos"
				]
			]
		];

		if ($row->children != null) {
			$this->relationships->children->links->related = "/galleries/{$row->children}";
		}
		if ($row->photos != null) {
			$this->relationships->photos->links->related = "/galleries/{$row->photos}";
		}

		$this->links = (object)[
			"self" => "/galleries/{$row->id}"
		];
	}

}