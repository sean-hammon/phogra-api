<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetAllIdColumnsToUnsigned extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //  Since I believe in using enum columns, Laravel's Schema is out because
        //  of its dependency on doctrine/dbal which can't handle enum columns...anywhere
        //  in the table being modified.
        //
        //  See http://stackoverflow.com/questions/33140860/laravel-5-1-unknown-database-type-enum-requested

        DB::statement('ALTER TABLE `galleries` CHANGE COLUMN `parent_id` `parent_id` INT(10) UNSIGNED NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `gallery_photos` CHANGE COLUMN `gallery_id` `gallery_id` INT(10) UNSIGNED NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `gallery_photos` CHANGE COLUMN `photo_id` `photo_id` INT(10) UNSIGNED NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `files` CHANGE COLUMN `photo_id` `photo_id` INT(10) UNSIGNED NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `gallery_users` CHANGE COLUMN `gallery_id` `gallery_id` INT(10) UNSIGNED NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `gallery_users` CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `galleries` CHANGE COLUMN `parent_id` `parent_id` INT(10) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `gallery_photos` CHANGE COLUMN `gallery_id` `gallery_id` INT(10) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `gallery_photos` CHANGE COLUMN `photo_id` `photo_id` INT(10) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `files` CHANGE COLUMN `photo_id` `photo_id` INT(10) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `gallery_users` CHANGE COLUMN `gallery_id` `gallery_id` INT(10) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE `gallery_users` CHANGE COLUMN `user_id` `user_id` INT(10) NULL DEFAULT NULL');
    }
}
