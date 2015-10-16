<?php

namespace App\Http\Controllers;

use App\Phogra\Response\BaseResponse;

class ApiController extends BaseController
{
    /**
     * Display a list of top level collections
     *
     * @return Response
     */
    public function index()
    {
        $response = new BaseResponse();
        $response->links->galleries = "/galleries";

        $response->data = (object)[
            "/galleries" => (object)[
                "examples" => (object)[
                    "single" => "/galleries/:id",
                    "multiple" => "/galleries/:id,:id,:id...",
                    "include" => "/galleries?include=photos,children",
                    "fields" => "galleries?fields=id,title,slug",
                    "empty" => "/galleries?empty=true"
                ],
                "parameters" => (object)[
                    "include" => (object)[
                        "description" => "Include full data for related models.",
                        "type" => "string: comma separated list",
                        "default" => "",
                        "accpeted" => [
                            "photos",
                            "children"
                        ]
                    ],
                    "fields" => (object)[
                        "description" => "Limit the columns returned by the query",
                        "type" => "string: comma separated list",
                        "default" => ""
                    ],
                    "empty" => (object)[
                        'description' => "Return galleries that have no photos in their tree (ie. the gallery and all it's children have no photos).",
                        'type' => 'boolean',
                        'default' => 'false'
                    ],
                ],
                "schema" => (object)[
                    'id' => 'integer',
                    'parent_id' => 'integer:nullable',
                    'title' => 'string(64):nullable',
                    'slug' => 'string(64): slug will be auto generated from title. If title left null, slug must be provided.',
                    'description' => 'text:nullable',
                    'protected' => 'boolean:default(0)'
                ],
            ]
        ];

        $response->links->photos = "/photos";

        return $response->send();
    }
}