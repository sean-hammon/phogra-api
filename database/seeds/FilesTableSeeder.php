<?php

use Illuminate\Database\Seeder;
use App\Phogra\File\Processor;
use App\Phogra\Eloquent\File;

class FilesTableSeeder extends Seeder
{
    public function run()
    {
        File::truncate();
        $this->cleanFiles();

        $data = [
            [
                "photo_id" => 1,
                "filename" => "DSC20041022-066.jpg"
            ],
            [
                "photo_id" => 24,
                "filename" => "DSC20041017-011.jpg"
            ],
            [
                "photo_id" => 6,
                "filename" => "DSC20041018-020.jpg"
            ],
            [
                "photo_id" => 23,
                "filename" => "DSC20041020-043.jpg"
            ],
            [
                "photo_id" => 2,
                "filename" => "DSC20070705-2823.jpg"
            ],
            [
                "photo_id" => 3,
                "filename" => "DSC20070705-2775.jpg"
            ],
            [
                "photo_id" => 9,
                "filename" => "DSC20070705-2783.jpg"
            ],
            [
                "photo_id" => 4,
                "filename" => "DSC20030729-007.jpg"
            ],
            [
                "photo_id" => 5,
                "filename" => "DSC20040705-009.jpg"
            ],
            [
                "photo_id" => 10,
                "filename" => "DSC20021027-028.jpg"
            ],
            [
                "photo_id" => 11,
                "filename" => "DSC20030729-001.jpg"
            ],
            [
                "photo_id" => 12,
                "filename" => "DSC20050514-432.jpg"
            ],
            [
                "photo_id" => 13,
                "filename" => "DSC20050522-470.jpg"
            ],
            [
                "photo_id" => 14,
                "filename" => "DSC20070416-031.jpg"
            ],
            [
                "photo_id" => 15,
                "filename" => "DSC20080302-060.jpg"
            ],
            [
                "photo_id" => 16,
                "filename" => "DSC20080716-2010.jpg"
            ],
            [
                "photo_id" => 17,
                "filename" => "DSC20080716-2033-HDR.jpg"
            ],
            [
                "photo_id" => 18,
                "filename" => "DSC20091017-003.jpg"
            ],
            [
                "photo_id" => 19,
                "filename" => "DSC20091017-015.jpg"
            ],
            [
                "photo_id" => 20,
                "filename" => "DSC20100704-042.jpg"
            ],
            [
                "photo_id" => 21,
                "filename" => "DSC20100814-119.jpg"
            ],
            [
                "photo_id" => 22,
                "filename" => "DSC20100814-128.jpg"
            ],
            [
                "photo_id" => 7,
                "filename" => "DSC20090719-153.jpg"
            ],
            [
                "photo_id" => 30,
                "filename" => "20090619-DSC_090619_031.jpg"
            ],
            [
                "photo_id" => 31,
                "filename" => "DSC20090718-123.jpg"
            ],
            [
                "photo_id" => 25,
                "filename" => "DSC20090327-022.jpg"
            ],
            [
                "photo_id" => 26,
                "filename" => "DSC20090327-023.jpg"
            ],
            [
                "photo_id" => 27,
                "filename" => "DSC20090328-008.jpg"
            ],
            [
                "photo_id" => 28,
                "filename" => "DSC20090328-017.jpg"
            ],
            [
                "photo_id" => 29,
                "filename" => "DSC20090328-052.jpg"
            ],
            [
                "photo_id" => 8,
                "filename" => "DSC20060327-192.jpg"
            ],
            [
                "photo_id" => 32,
                "filename" => "DSC20060327-194.jpg"
            ],
            [
                'photo_id' => 82,
                'filename' => 'milford-sound-90e5.jpg',
            ], [
                'id' => 83,
                'filename' => 'milford-sound-46c5.jpg',
            ], [
                'id' => 84,
                'filename' => 'milford-sound-a929.jpg',
            ], [
                'id' => 85,
                'filename' => 'seals-chilling-in-milford-sound.jpg',
            ], [
                'filename' => 167,
                'slug' => 'falls-in-milford-sound.jpg',
            ], [
                'id' => 168,
                'slug' => 'somewhere-in-southland.jpg',
            ],
            [
                'id' => 56,
                'slug' => 'a-blue-door.jpg',
            ], [
                'id' => 57,
                'slug' => 'a-yellow-door.jpg',
            ], [
                'id' => 59,
                'slug' => 'hobbits-love-yellow.jpg',
            ], [
                'id' => 61,
                'slug' => 'bag-end.jpg',
            ], [
                'id' => 66,
                'slug' => 'the-party-tree-from-the-green-dragon.jpg',
            ]
        ];

        foreach ($data as $photo) {
            echo "{$photo['photo_id']}:{$photo['filename']}\n";
            $path = "seed-photos/" . $photo['filename'];
            $processor = new Processor($path);
            $processor->make('original');
            $processor->setPhotoId($photo['photo_id']);
            $processor->storeFile();

            $typeConfig = config('phogra.fileTypes');
            foreach ($typeConfig->original->autoGenerate as $type) {
                $processor->make($type);
                $processor->setPhotoId($photo['photo_id']);
                $processor->storeFile();
            }
            unset($processor);
        }
    }

    private function cleanFiles()
    {
        $pathsToClean[] = config('phogra.photoDir');
        $pathsToClean[] = config('phogra.photoTempDir');

        foreach ($pathsToClean as $path) {
            if (PHP_OS === 'Windows') {
                exec("rd /s /q {$path}\*.*");
            } else {
                exec("rm -rf {$path}/*");
            }
        }
    }
}
