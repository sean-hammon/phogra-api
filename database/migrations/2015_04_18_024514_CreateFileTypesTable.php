<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
			$types = get_object_vars(config('phogra.fileTypes'));
			$typeKeys = array_keys($types);
			$default = $typeKeys[0];
			$table->increments('id');
			$table->enum("type", $typeKeys)->default($default);
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
