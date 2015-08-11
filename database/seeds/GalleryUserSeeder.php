<?php

use Illuminate\Database\Seeder;

class GalleryUserSeeder extends Seeder
{
	public function run()
	{
		DB::statement('truncate table gallery_users');
		DB::table('gallery_users')->insert([
			['gallery_id' => 21, 'user_id' => 1],
			['gallery_id' => 21, 'user_id' => 2],
			['gallery_id' => 22, 'user_id' => 1],
	    ]);
	}
}