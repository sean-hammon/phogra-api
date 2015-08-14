<?php

namespace App\Phogra\Response;

class Photo
{
	var $type = 'photos';
	var $id = null;
	var $attributes;
	var $relationships;
	var $links;

	public function __construct($row) {

		$this->id = $row->id;
		$this->attributes = (object)[
			'title' => $row->title,
			'slug' => $row->slug,
			'short_desc' => $row->short_desc,
			'long_desc' => $row->long_desc,
			'created_at' => $row->created_at,
			'updated_at' => $row->updated_at
		];

		$this->relationships = (object)[
			"files" => (object)[
				"type"  => "files",
				"data" => ($row->file_types == null ? null : explode(',', $row->file_types)),
				"links" => (object)[
					"self" => "/photos/{$row->id}/files"
				]
			]
		];

		$this->links = (object)[
			"self" => "/photos/{$row->id}"
		];
	}

}