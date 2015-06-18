<?php

use Illuminate\Database\Seeder;
use App\Phogra\User;

class UserTableSeeder extends Seeder
{
	public function run()
	{
		DB::statement('truncate table users');
		User::create([
			"email" => 'sean@sean-hammon.com',
			"password" => 'foobar'
	    ]);
	}
}