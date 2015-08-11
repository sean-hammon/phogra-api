<?php

use Illuminate\Database\Seeder;
use App\Phogra\Eloquent\User;

class UserTableSeeder extends Seeder
{
	public function run()
	{
		DB::statement('truncate table users');
		User::create([
			"name" => 'Dummy Admin',
			"email" => 'admin@example.com',
			"password" => Hash::make('foobar'),
			"is_admin" => 1,
			"api_token" => Hash::make('Supercalifragilisticexpialidocious')
	    ]);
		User::create([
			"name" => 'Dummy User',
			"email" => 'dummy@example.com',
			"password" => Hash::make('foobartoo'),
			"is_admin" => 0,
			"api_token" => Hash::make('pneumonoultramicroscopicsilicovolcanoconiosis')
		]);
	}
}