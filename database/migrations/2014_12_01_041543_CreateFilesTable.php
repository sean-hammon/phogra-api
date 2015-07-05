<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFilesTable
 *
 * File type in an enum based on the keys defined in the config/phogra.php file.
 */
class CreateFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('files', function(Blueprint $table)
		{
			$types = get_object_vars(config('phogra.fileTypes'));
			$typeKeys = array_keys($types);
			$default = $typeKeys[0];
			$table->increments('id');
			$table->enum('type', $typeKeys)->default($default);
			$table->smallInteger('bytes')->nullable();
			$table->string('hash', 60)->unique();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('files');
	}

}
