<?php

use Illuminate\Database\Seeder;


class GalleryPhotosTableSeeder extends Seeder
{
	public function run()
	{
		DB::statement('truncate table gallery_photos');
		$data = [
			[
				"photo_id" => 1,
				"gallery_id" => 19
			],
			[
				"photo_id" => 2,
				"gallery_id" => 14
			],
			[
				"photo_id" => 3,
				"gallery_id" => 14
			],
			[
				"photo_id" => 4,
				"gallery_id" => 5
			],
			[
				"photo_id" => 5,
				"gallery_id" => 5
			],
			[
				"photo_id" => 6,
				"gallery_id" => 19
			],
			[
				"photo_id" => 7,
				"gallery_id" => 4
			],
			[
				"photo_id" => 8,
				"gallery_id" => 8
			]
		];
	}
}