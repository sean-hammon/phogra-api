<?php

namespace App\Phogra\Response\Item;

use Hashids;

class User extends ResponseItem
{
    public function __construct($row)
    {
        parent::__construct();

        $this->type = 'user';

        $this->id = Hashids::encode($row->id);
        $this->attributes = (object)[
            'id' => $row->id,
            'name' => $row->name,
            'email' => $row->email,
            'admin' => $row->admin,
            'hash' => $row->hash,
            'token' => $row->token,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at
        ];

        $this->relationships = (object)[
            "galleries" => (object)[
                "type"  => "gallery",
                "data" => ($row->galleries == null ? null : explode(',', $row->galleries)),
                "links" => (object)[
                    "self" => $this->baseUrl . "/users/{$this->id}/galleries"
                ]
            ]
        ];

        $this->links = (object)[
            "self" => $this->baseUrl . "/users/{$this->id}"
        ];
    }
}