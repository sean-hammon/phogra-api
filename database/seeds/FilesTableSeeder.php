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
                "DSC20041022-066.jpg"
            ],
            [
                "photo_id" => 24,
                "DSC20041017-011.jpg"
            ],
            [
                "photo_id" => 6,
                "DSC20041018-020.jpg"
            ],
            [
                "photo_id" => 23,
                "DSC20091020-43.jpg"
            ],
            [
                "photo_id" => 2,
                "DSC20070705-2823.jpg"
            ],
            [
                "photo_id" => 3,
                "DSC20070705-2775.jpg"
            ],
            [
                "photo_id" => 9,
                "DSC20070705-2793.jpg"
            ],
            [
                "photo_id" => 4,
                "DSC20030729-007.jpg"
            ],
            [
                "photo_id" => 5,
                "DSC20040705-009.jpg"
            ],
            [
                "photo_id" => 10,
                "DSC20021027-028.jpg"
            ],
            [
                "photo_id" => 11,
                "DSC20030729-001.jpg"
            ],
            [
                "photo_id" => 12,
                "DSC20050514-432.jpg"
            ],
            [
                "photo_id" => 13,
                "DSC20050522-470.jpg"
            ],
            [
                "photo_id" => 14,
                "DSC20050522-470.jpg"
            ],
            [
                "photo_id" => 15,
                "DSC20080302-060.jpg"
            ],
            [
                "photo_id" => 16,
                "DSC20080716-2010.jpg"
            ],
            [
                "photo_id" => 17,
                "DSC20080716-2033-HDR.jpg"
            ],
            [
                "photo_id" => 18,
                "DSC20091017-003.jpg"
            ],
            [
                "photo_id" => 19,
                "DSC20091017-015.jpg"
            ],
            [
                "photo_id" => 20,
                "DSC20100704-042.jpg"
            ],
            [
                "photo_id" => 21,
                "DSC20091017-003.jpg"
            ],
            [
                "photo_id" => 22,
                "DSC20091017-003.jpg"
            ],
            [
                "photo_id" => 7,
                "20090719-153.jpg"
            ],
            [
                "photo_id" => 30,
                "20090619-DSC090619_031.jpg"
            ],
            [
                "photo_id" => 31,
                "DSC20090713-123.jpg"
            ],
            [
                "photo_id" => 25,
                "DSC20090327-022.jpg"
            ],
            [
                "photo_id" => 26,
                "DSC20090327-023.jpg"
            ],
            [
                "photo_id" => 27,
                "DSC20090327-023.jpg"
            ],
            [
                "photo_id" => 28,
                "DSC20090327-023.jpg"
            ],
            [
                "photo_id" => 29,
                "DSC20090327-023.jpg"
            ],
            [
                "photo_id" => 8,
                "DSC20060327-192.jpg"
            ],
            [
                "photo_id" => 32,
                "DSC20060327-195.jpg"
            ]
		];

        foreach ($data as $file) {
            $path = "../../seed-photos/" . $$file->filename;
            $upload = new Upload($file->photo_id, $path);
        }
    }
}