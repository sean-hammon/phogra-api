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

	'fileTypes'    => (object)[
		'original' => (object)[
			'height' => null,
			'width'  => null,
			'autoGenerate' => ['2k', '1k-l', '1k-p', 'thumb']
		],
		'4k' =>	(object)[
			'height' => null,
			'width'  => 3840,
			'autoGenerate' => ['2k', '1k-l', '1k-p', 'thumb']
		],
		'2k' => (object)[
			'height' => null,
			'width'  => 1980,
			'autoGenerate' => ['1k-l', '1k-p', 'thumb']
		],
		'1k-l' => (object)[
			'height' => null,
			'width'  => 990,
			'autoGenerate' => ['thumb']
		],
		'1k-p' => (object)[
			'height'  => 990,
			'width' => null,
			'autoGenerate' => ['thumb']
		],
		'thumb' => (object)[
			'height' => 320,
			'width'  => 320,
			'autoGenerate' => []
		]
	],

	//  Directories for file storage
	//
	//  If the photoDir is in the public_path, the API will return URLs pointing
	//  directly to the image file.
	//
	//  If the photoDir is outside the public path,
	//  the API will return an API endpoint that will use readfile() to return
	//	the image data.

	'photoDir'       => public_path() . DIRECTORY_SEPARATOR . "photos",
	'photoTempDir'   => storage_path() . DIRECTORY_SEPARATOR . "photo-tmp",
	'publicApi'      => false,
	'apiTokenHeader' => 'X-Phogra-Token'
);