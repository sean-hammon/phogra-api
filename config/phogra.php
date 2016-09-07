<?php

return array(
    //  File types available. Types are used by the api to retrieve files for specific
    //  contexts, such as retina or mobile. The file types migration and seeder read this file.
    //
    //  Sizes are defaults for auto-generation.
    //	One dimension given:
    //		If dimension given is the same as the longest edge,
    //			proportional scaling will be applied.
    //		If dimension given is not the longest edge,
    //			the image will be scaled and then cropped.
    //          (ie. a portrait sized piece will be cropped out of a landscape image)
    //	Both dimensions given:
    // 		The image will be scaled cropped.

    //	TODO: refactor this because we aren't auto-generating any more.
	//  TODO: look into using queues to auto-generate in a shared-host-friendly manner

    'fileTypes' => (object)[
        'original' => (object)[
            'height' => null,
            'width' => null,
            'autoGenerate' => ['hifi', 'lofi', 'thumb', 'so']
        ],
        'ulfi' => (object)[
            'height' => null,
            'width' => 3840,
	        'autoGenerate' => ['hifi', 'lofi', 'thumb', 'so']
        ],
        'hifi' => (object)[
            'height' => null,
            'width' => 1920,
	        'autoGenerate' => ['lofi', 'thumb', 'so']
        ],
        'lofi' => (object)[
            'height' => null,
            'width' => 990,
            'autoGenerate' => ['so', 'thumb']
        ],
        'thumb' => (object)[
            'height' => 320,
            'width' => 320,
            'autoGenerate' => []
        ]
    ],

    //  Make sure you don't create a folder in the public folder that matches a route, eg.
    //  photos or galleries. This will cause you heartburn.

    'photoDir' => storage_path("app/photos"),
    'photoTempDir' => storage_path("app/photo-tmp"),

    //	The name of the HTTP header where the JWT token is returned.
    'apiTokenHeader' => 'X-Phogra-Token',
    //	Domains to allow AJAX requests from, if you specify a domain, it must be fully qualified.
    //	Remember that http://foo.com is considered a different domain from https://foo.com.
    'allowedDomains' => ['*']
);