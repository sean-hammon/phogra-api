<?php

use Illuminate\Database\Seeder;
use App\Phogra\Gallery;
use App\Phogra\Eloquent\Gallery as GalleryTable;

class GalleryTableSeeder extends Seeder
{

    public function run()
    {
        GalleryTable::truncate();

        $galleries = [
            [
                'id' => 1,
                'parent_id' => null,
                'title'     => 'Outdoors',
                'children'  => [
                    [
                        'id'       => 2,
                        'title'    => 'United States',
                        'children' => [
                            [
                                "id"    => 3,
                                "title" => 'California'
                            ],
                            [
                                "id"    => 4,
                                "title" => 'Colorado',
                            ],
                            [
                                "id"    => 5,
                                "title" => 'Utah',
                            ],
                            [
                                "id"    => 6,
                                "title" => 'Florida'
                            ],
                            [
                                "id" => 19,
                                "title" => 'Minnesota'
                            ],
                            [
                                "id" => 20,
                                "title" => 'Illinois'
                            ]
                        ]
                    ],
                    [
                        'id'       => 7,
                        'title'    => 'Canada',
                        'children' => [
                            [
                                'id'    => 8,
                                'title' => 'Alberta'
                            ]
                        ]
                    ],
                    [
                        'id'       => 9,
                        'title'    => 'New Zealand',
                        'children' => [
                            [
                                'id'    => 10,
                                'title' => 'Hobbiton'
                            ],
                            [
                                'id'    => 11,
                                'title' => 'Milford Sound'
                            ],
                            [
                                'id'    => 12,
                                'title' => 'Queenstown'
                            ]
                        ]
                    ],
                    [
                        'id' => 13,
                        'title' => 'Mexico',
                        'children' => [
                            [
                                'id' => 14,
                                'title' => 'Teotihuacan'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 15,
                'title' => 'Portraits'
            ],
            [
                'id' => 16,
                'title' => 'Sports',
                'children' => [
                    [
                        'id' => 17,
                        'title' => 'Speed Skating'
                    ],
                    [
                        'id' => 18,
                        'title' => 'Swimming'
                    ]
                ]
            ],
            [
                'id' => 21,
                'title' => 'Family',
                'protected' => 1,
                'children' => [
                    [
                        'id' => 22,
                        'title' => 'Grandpa\'s Funeral'
                    ]
                ]
            ]
        ];

        $this->makeGalleries($galleries);
    }

    private function makeGalleries($galleries, $parent = null)
    {
        foreach ($galleries as $g) {
            $aGallery = new Gallery([
                'id' => $g['id'],
                'parent_id' => ($parent == null ? null : $parent->id),
                'title' => $g['title']
            ]);
            $row = $aGallery->create();
            if (!empty($g['children'])) {
                $this->makeGalleries($g['children'], $row);
            }
        }
    }
}