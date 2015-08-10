<?php

use Illuminate\Database\Seeder;
use App\Phogra\Eloquent\User;

class UserTableSeeder extends Seeder
{
	public function run()
	{
		DB::statement('truncate table users');
		User::create([
			"name" => 'Dummy User',
			"email" => 'dummy@example.com',
			"password" => 'foobar'
	    ]);
	}
}