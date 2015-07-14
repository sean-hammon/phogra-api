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
			$table->integer('photo_id')->index('files_photo_id');
			$table->enum('type', $typeKeys)->default($default);
			$table->string('mimetype', 32);
			$table->smallInteger('height')->unsigned()->nullable();
			$table->smallInteger('width')->unsigned()->nullable();
			$table->integer('bytes')->unsigned()->nullable();
			$table->char('hash', 64)->unique();
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
