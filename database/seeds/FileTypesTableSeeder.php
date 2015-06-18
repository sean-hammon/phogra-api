<?php

use Illuminate\Database\Seeder;
use App\Phogra\FileType;

class FileTypesTableSeeder extends Seeder
{

    public function run()
    {
        FileType::truncate();

        FileType::create([
            "type" => "original"
        ]);
        FileType::create([
            "type" => "fit1080"
        ]);
        FileType::create([
            "type" => "fit1920"
        ]);
        FileType::create([
            "type" => "1080p",
            "height" => 1080,
            "width" => 1920
        ]);
        FileType::create([
            "type" => "540p",
            "height" => 540,
            "width" => 960
        ]);
        FileType::create([
            "type" => "thumb",
            "height" => 320,
            "width" => 320
        ]);
    }

}