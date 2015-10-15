<?php

namespace App\Phogra\Response\Item;

use App\Phogra\Eloquent\File as FileModel;
use Hashids;

class File extends ResponseItem
{
    public function __construct($row, $included = false)
    {

        parent::__construct();

        $this->type = 'files';
        $this->id = (isset($row->id) ? $row->id : $row->file_id);
        $this->id = Hashids::encode($this->id);

        $photoDir = config('phogra.photoDir');
        if (strpos($photoDir, public_path()) !== false) {
            $model = new FileModel();
            $model->hash = $row->hash;
            $model->mimetype = $row->mimetype;
            $href = "/" . str_replace("\\", "/", $model->location());
        } else {
            $href = null;
        }

        $this->attributes = (object)[
            'photo_id' => (isset($row->photo_id) ? $row->photo_id : $row->id),
            'type' => $row->type,
            'mimetype' => $row->mimetype,
            'height' => $row->height,
            'width' => $row->width,
            'bytes' => $row->bytes,
            'created_at' => (isset($row->created_at) ? $row->created_at : $row->file_created_at),
            'updated_at' => (isset($row->updated_at) ? $row->updated_at : $row->file_updated_at),
        ];
        $this->attributes->photo_id = Hashids::encode($this->attributes->photo_id);

        $this->links = (object)[
            "self" => $this->baseUrl . "/photos/{$this->attributes->photo_id}/files/{$this->attributes->type}",
            "image" => $this->baseUrl . "/photos/{$this->attributes->photo_id}/image/{$this->attributes->type}",
            "src" => ($href ? $this->baseUrl . $href : null)
        ];

        if (!$included) {
            $this->relationships = (object)[
                "photo" => (object)[
                    "type" => "photos",
                    "data" => $this->attributes->photo_id,
                    "links" => (object)[
                        "self" => $this->baseUrl . "/photos/{$this->attributes->photo_id}/files/{$this->type}",
                        "image" => $this->baseUrl . "/photos/{$this->attributes->photo_id}/image/{$this->type}",
                        "src" => $this->baseUrl . $href
                    ]
                ]
            ];
        } else {
            unset($this->relationships);
        }
    }
}