<?php

namespace App\Phogra\Response;

class Photo extends BaseResponse
{
	var $type = 'photos';
	var $id = null;
	var $attributes;
	var $relationships;
	var $links;

	public function __construct($row) {
		parent::__construct();

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
				"data" => ($row->file_ids == null ? null : explode(',', $row->file_ids)),
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