<?php

use Illuminate\Database\Seeder;
use App\Phogra\FileType;

class FileTypesTableSeeder extends Seeder
{

    public function run()
    {
        FileType::truncate();

		$typeConfig = get_object_vars(config('phogra.fileTypes'));
		$types = array_keys($typeConfig);
		foreach ($types as $type) {
			$data = get_object_vars($typeConfig[$type]);
			$data['name'] = $type;
			FileType::create($data);
		}

    }

}