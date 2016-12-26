<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactorFileTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = new DateTime();
        DB::table("files")
          ->where('type', 'like', '%_p')
          ->update(['deleted_at' => $now->format('Y-m-d H:i:s')]);
        DB::statement("ALTER TABLE `files` CHANGE COLUMN `type` `type` ENUM('original','ulfi_l','ulfi_p','hifi_l','hifi_p','lofi_l','lofi_p','thumb','so','hifi','lofi','ulfi') NOT NULL DEFAULT 'original' COLLATE 'utf8_unicode_ci' AFTER `photo_id`;");
        DB::table("files")
          ->whereIn('type', ['hifi_l', 'hifi_p'])
          ->update(['type' => 'hifi']);
        DB::table("files")
          ->whereIn('type', ['lofi_l', 'lofi_p'])
          ->update(['type' => 'lofi']);
        DB::table("files")
          ->whereIn('type', ['ulfi_l', 'ulfi_p'])
          ->update(['type' => 'ulfi']);
        DB::statement("ALTER TABLE `files` CHANGE COLUMN `type` `type` ENUM('original','thumb','so','hifi','lofi','ulfi') NOT NULL DEFAULT 'original' COLLATE 'utf8_unicode_ci' AFTER `photo_id`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `files` CHANGE COLUMN `type` `type` ENUM('original','ulfi_l','ulfi_p','hifi_l','hifi_p','lofi_l','lofi_p','thumb','so','hifi','lofi','ulfi') NOT NULL DEFAULT 'original' COLLATE 'utf8_unicode_ci' AFTER `photo_id`;");
        DB::table("files")
          ->where('type', 'hifi')
          ->where('width', '>=', 'height')
          ->update(['type' => 'hifi_l']);
        DB::table("files")
          ->where('type', 'hifi')
          ->where('height', '>', 'width')
          ->update(['type' => 'hifi_p']);
        DB::table("files")
          ->where('type', 'lofi')
          ->where('width', '>=', 'height')
          ->update(['type' => 'lofi_l']);
        DB::table("files")
          ->where('type', 'lofi')
          ->where('height', '>', 'width')
          ->update(['type' => 'lofi_p']);
        DB::table("files")
          ->where('type', 'ulfi')
          ->where('width', '>=', 'height')
          ->update(['type' => 'ulfi_l']);
        DB::table("files")
          ->where('type', 'ulfi')
          ->where('height', '>', 'width')
          ->update(['type' => 'ulfi_p']);
        DB::statement("ALTER TABLE `files` CHANGE COLUMN `type` `type` ENUM('original','ulfi_l','ulfi_p','hifi_l','hifi_p','lofi_l','lofi_p','thumb') NOT NULL DEFAULT 'original' COLLATE 'utf8_unicode_ci' AFTER `photo_id`;");
        DB::table("files")
          ->where('type', 'like', '%_p')
          ->update(['deleted_at' => null]);
    }
}
