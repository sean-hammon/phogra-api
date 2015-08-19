<?php

namespace App\Phogra\Response\Item;

class Photo extends ResponseItem
{
	public function __construct($row) {

		$this->type = 'photos';

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

	public function addFile($row) {
		$this->included[] = new File($row, true);
	}

}