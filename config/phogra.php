<?php

return array(
	//  File types available. Keys used by CreateFileTypes migration.
	//
	//  Sizes are defaults for auto-generation.
	//  Original size isn't used. It's just here to make testing more straight forward.
	//
	//	If only one dimension is supplied, proportional scaling is applied.
	//
	//	Two files for each type are supported for mobile. If this is desired,
	//  the property 'longest' will generate landscape and portrait files with
	//  the long edge matching the given dimension. The resulting files wil
	//  be recorded as {filetype}-l and {filetype}-p
	'fileTypes'    => (object)[
		'original' => (object)[
			'height' => null,
			'width'  => null
		],
		'4k' =>	(object)[
			'width'  => 3840
		],
		'2k' => (object)[
			'width'  => 1980

		],
		'1k' => (object)[
			'longest'  => 990
		],
		'thumb' => (object)[
			'height' => 320,
			'width'  => 320
		]
	],

	//  Directories for file storage
	'photoDir'     => base_path() . DIRECTORY_SEPARATOR . "photos",
	'photoTempDir' => base_path() . DIRECTORY_SEPARATOR . "photos" . DIRECTORY_SEPARATOR . "tmp"
);