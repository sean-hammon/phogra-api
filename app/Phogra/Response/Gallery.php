<?php

namespace App\Phogra\Response;

class Gallery
{
	var $data;
	var $relationships;

	public function __construct($row) {
		$this->data = (object)[
			"type"       => "galleries",
			"id"         => $row->id,
			"attributes" => (object)[

			]
		];

		$this->relationships = (object)[
			"children" => (object)[
				"type"  => "galleries",
				"ids"   => [],
				"links" => (object)[
					"related" => "/galleries/{$row->id}/children"
				]
			],
			"photos" => (object)[
				"type"  => "photos",
				"ids"   => [],
				"links" => (object)[
					"related" =>  "/galleries/{$row->id}/photos"
				]
			]
		];
	}

	public function setChildren($children) {

	}

	public function setPhotos($photos) {

	}

}