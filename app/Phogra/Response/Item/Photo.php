<?php

namespace App\Phogra\Response\Item;

use Hashids;

class Photo extends ResponseItem
{
    public function __construct($row)
    {

        parent::__construct();

        $this->type = 'photos';

        $this->id = Hashids::encode($row->id);
        $this->attributes = (object)[
            'title' => $row->title,
            'slug' => $row->slug,
            'short_desc' => $row->short_desc,
            'long_desc' => $row->long_desc,
            'canonical_gallery_id' => Hashids::encode($row->canonical_gallery_id),
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at
        ];

        $this->relationships = (object)[
            "files" => (object)[
                "type" => "files",
                "data" => ($row->file_types == null ? null : explode(',', $row->file_types)),
                "links" => (object)[
                    "self" => $this->baseUrl . "/photos/{$this->id}/files"
                ]
            ]
        ];

        $this->links = (object)[
            "self" => $this->baseUrl . "/photos/{$this->id}"
        ];
    }

    public function addFile($row)
    {
        $this->included[] = new File($row, true);
    }

}