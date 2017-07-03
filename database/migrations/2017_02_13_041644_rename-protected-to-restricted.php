<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameProtectedToRestricted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('galleries', function (Blueprint $table) {
            DB::statement('ALTER TABLE `galleries` CHANGE COLUMN `protected` `restricted` TINYINT(1) NOT NULL DEFAULT 0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('galleries', function (Blueprint $table) {
            DB::statement('ALTER TABLE `galleries` CHANGE COLUMN `restricted` `protected` TINYINT(1) NOT NULL DEFAULT 0');
        });
    }
}
