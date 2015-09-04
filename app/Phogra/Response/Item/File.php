<?php

namespace App\Phogra\Response\Item;

use App\Phogra\Eloquent\File as FileModel;
use Hashids;

class File extends ResponseItem
{
	public function __construct($row, $included = false) {

		parent::__construct();

		$this->type = 'files';
		$this->id = (isset($row->id) ? $row->id : $row->file_id);
		$this->id = Hashid::encode($this->id);

		$photoDir = config('phogra.photoDir');
		if (strpos($photoDir, public_path()) !== false) {
			$model = new FileModel();
			$model->hash = $row->hash;
			$model->mimetype = $row->mimetype;
			$href = "/" . $model->location();
		} else {
			$href = "/files/{$this->id}/image";
		}

		$this->attributes = (object)[
			'photo_id' => (isset($row->photo_id) ? $row->photo_id : $row->id),
			'type' => $row->type,
			'mimetype' => $row->mimetype,
			'height' => $row->height,
			'width' => $row->width,
			'bytes' => $row->bytes,
			'href' => $this->baseUrl . $href,
			'created_at' => (isset($row->created_at) ? $row->created_at : $row->file_created_at),
			'updated_at' => (isset($row->updated_at) ? $row->updated_at : $row->file_updated_at),
		];
		$this->attributes->photo_id = Hashids::encode($this->attributes->photo_id);

		$this->links = (object)[
			"self" => $this->baseUrl . "/photos/{$this->attributes->photo_id}/files/{$this->attributes->type}"
		];

		if (!$included) {
			$this->relationships = (object)[
				"photo" => (object)[
					"type"  => "photos",
					"data" => $this->attributes->photo_id,
					"links" => (object)[
						"self" => $this->baseUrl . "/photos/{$this->attributes->photo_id}"
					]
				]
			];
		} else {
			unset($this->relationships);
		}
	}
}