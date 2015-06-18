<?php

use App\Phogra\File\Upload;

class FilesTableSeeder
{
    public function run()
    {
        File::truncate();

        $data = [
            [
                "photo_id" => 1,
                "filename" => 'DSC20041022-066.jpg'
            ],
            [
                'photo_id' => 2,
                'filename' => 'DSC20070705-2823.jpg'
            ],
            [
                'photo_id' => 3,
                'filename' => 'DSC20070705-2775.jpg'
            ],
            [
                'photo_id' => 4,
                'filename' => 'DSC20030729-007.jpg'
            ],
            [
                'photo_id' => 5,
                'filename' => 'DSC20040705-009.jpg'
            ],
            [
                'photo_id' => 6,
                'filename' => 'DSC20041017-011.jpg'
            ],
            [
                'photo_id' => 7,
                'filename' => 'DSC20090719-153.jpg'
            ],
            [
                'photo_id' => 8,
                'filename' => 'DSC20060328-234.jpg'
            ]
        ];

    }
}