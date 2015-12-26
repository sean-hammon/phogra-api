<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('photos', function($table){
            $table->increments('id');
            $table->string('title', 64)->nullable();
            $table->string('slug', 64)->unique();
			$table->string('short_desc', 512)->nullable();
            $table->text('long_desc')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('photos');
	}

}
