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

	    if (!is_string($row->tags)) {

		    //  TODO: On creation this is coming back as a Laravel collection.
		    //  Will track that down later. Just handle it for now.
		    $names_only = $row->tags->map(function($item){
			    return $item->name;
		    });
		    $row->tags = implode($names_only->all(), ',');
	    }
        $this->relationships = (object)[
            "files" => (object)[
                "type" => "files",
                "data" => ($row->file_types == null ? null : explode(',', $row->file_types)),
                "links" => (object)[
                    "self" => $this->baseUrl . "/photos/{$this->id}/files"
                ]
            ],
            "tags" => (object)[
				"type" => "tags",
				"data" => ($row->tags == null ? null : explode(',', $row->tags)),
				"links" => (object)[
					"self" => $this->baseUrl . "/photos/{$this->id}/tags"
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