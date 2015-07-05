<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFileTypesTable
 *
 * File types are defined in the config/phogra.php file and populated by the seeder.
 * The same array is used to create an enum field in the files table.
 */
class CreateFileTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('file_types', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string("name", 16)->unique();
			$table->smallInteger('height')->nullable();
			$table->smallInteger('width')->nullable();
			$table->smallInteger('longest')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('file_types');
	}

}
