<?php

use App\Phogra\Eloquent\Gallery;
use App\Phogra\Eloquent\Photo;
use Illuminate\Database\Seeder;

class PhotoTableSeeder extends Seeder
{
    public function run()
    {
        Photo::truncate();
        $galleries = array(
            [
                "id" => 4,
                "photos" => [
                    [
                        "id" => 7,
                        "title" => 'Paradox Valley',
                        "slug" => 'paradox-valley',
                        'short_desc' => "A hidden gem in southwest Colorado. Had no idea such a beautiful little valley existed.",
                        'long_desc' => ''
                        // DSC20090719-153
                    ],
                    [
                        "id" => 30,
                        "title" => 'Ute Canyon, Colorado National Monument',
                        "slug" => 'ute-canyon-colorado-national-monument',
                        'short_desc' => "Ute Canyon overlook on the south end of the park.",
                        'long_desc' => ''
                        // 20090619-DSC_090619_031
                    ],
                    [
                        "id" => 31,
                        "title" => 'Sunset over McPhee Reservoir',
                        "slug" => 'sunset-over-mcphee-reservoir',
                        'short_desc' => "Just an amazing summer sky at sunset.",
                        'long_desc' => ''
                        // DSC20090718-123
                    ]
                ]
            ],
            [
                "id" => 6,
                "photos" => [
                    [
                        "id" => 25,
                        "title" => 'Sunbathing',
                        "slug" => 'sunbathing',
                        'short_desc' => "A little guy soaking up some sun.",
                        'long_desc' => ''
                        // DSC20090327-022
                    ],
                    [
                        "id" => 26,
                        "title" => 'Sky on Fire',
                        "slug" => 'sky-on-fire',
                        'short_desc' => "A tricky shot that took a fair amount of work in Photoshop to get right.",
                        'long_desc' => ''
                        // DSC20090327-023
                    ],
                    [
                        "id" => 27,
                        "title" => 'Big Ball of Fire',
                        "slug" => 'big-ball-of-fire',
                        'short_desc' => "Sunrise on the water.",
                        'long_desc' => ''
                        // DSC20090328-008
                    ],
                    [
                        "id" => 28,
                        "title" => 'Kayaking in the Florida Keys',
                        "slug" => 'kayaking-in-the-florida-keys',
                        'short_desc' => "Stopping on a small island for a look-see.",
                        'long_desc' => ''
                        // DSC20090328-017
                    ],
                    [
                        "id" => 29,
                        "title" => 'Too Windy to Fly',
                        "slug" => 'too-windy-to-fly',
                        'short_desc' => "Terns waiting for the wind to settle down.",
                        'long_desc' => ''
                        // DSC20090328-052
                    ]
                ]
            ],
            [
                "id" => 19,
                "photos" => [
                    [
                        "id" => 1,
                        "title" => 'Fall Riot',
                        "slug" => 'fall-riot',
                        "short_desc" => 'Flaming reds in the ground cover at BWCA',
                        "long_desc" => 'Found this wandering around camp while canoeing in the BWCA. There was just something about the pattern in the small leaves and the contrast of the red against the green that drew my eye.'
                        // DSC20041022-066
                    ],
                    [
                        "id" => 24,
                        "title" => 'Making Camp',
                        "slug" => 'making-camp',
                        'short_desc' => "Canoes pulled out of the water after a day of paddling.",
                        'long_desc' => ''
                        // DSC20041017-011
                    ],
                    [
                        "id" => 6,
                        "title" => 'Canoes Resting',
                        "slug" => 'canoes-resting',
                        'short_desc' => "Canoes waiting on shore while we eat some lunch.",
                        'long_desc' => ''
                        // DSC20041018-020
                    ],
                    [
                        "id" => 23,
                        "title" => 'Mirrored Sunset',
                        "slug" => 'mirrored-sunset',
                        'short_desc' => "Sunset during one of my trips to the Boundary Waters.",
                        'long_desc' => ''
                        // DSC20041020-043
                    ]
                ]
            ],
            [
                "id" => 5,
                "photos" => [
                    [
                        "id" => 4,
                        "title" => 'Squirrel of Caerbannog',
                        "slug" => 'squirrel-of-caerbannog',
                        'short_desc' => "Fortunately I didn't have to use my Holy Hand Grenade of Antioch",
                        'long_desc' => ''
                        // DSC20030729-007
                    ],
                    [
                        "id" => 5,
                        "title" => 'Doughnut Falls',
                        "slug" => 'doughnut-falls',
                        'short_desc' => "",
                        'long_desc' => ''
                        // DSC20040705-009
                    ],
                    [
                        "id" => 10,
                        "title" => 'Bright Yellow',
                        "slug" => 'bright-yellow',
                        'short_desc' => "A blazing arc of aspens in the Utah mountains.",
                        'long_desc' => ''
                        // DSC20021027-028
                    ],
                    [
                        "id" => 11,
                        "title" => 'Mallard Noir',
                        "slug" => 'mallard-noir',
                        'short_desc' => "I liked the composition, but it wasn't much of a photo. Heavy post processing later...voila.",
                        'long_desc' => ''
                        // DSC20030729-001
                    ],
                    [
                        "id" => 12,
                        "title" => 'Lone Peak',
                        "slug" => 'lone-peak',
                        'short_desc' => "It may be Spring in the valley, but there is plenty of snow still in the mountains in May.",
                        'long_desc' => ''
                        // DSC20050514-432
                    ],
                    [
                        "id" => 13,
                        "title" => 'Aspens in Spring',
                        "slug" => 'aspens-in-spring',
                        'short_desc' => "Not sure what it is about aspens that is so mesmerizing.",
                        'long_desc' => ''
                        // DSC20050522-470
                    ],
                    [
                        "id" => 14,
                        "title" => 'Mount Timpanogos',
                        "slug" => 'mount-timpanogos',
                        'short_desc' => "Mt. Timpanogos is a prominent feature of Utah County and, like any mountain peak, has many moods.",
                        'long_desc' => ''
                        // DSC20070416-031
                    ],
                    [
                        "id" => 15,
                        "title" => 'Water falling near Lake Blanche',
                        "slug" => 'water-falling-near-lake blanche',
                        'short_desc' => "The date on the photo says March. That must mean we had almost no snow that year.",
                        'long_desc' => ''
                        // DSC20080302-060
                    ],
                    [
                        "id" => 16,
                        "title" => 'Red rock near Moab',
                        "slug" => 'red-rock-near-moab',
                        'short_desc' => "This canyon south of Moab accessible only by dirt road...or mountain bike.",
                        'long_desc' => ''
                        // DSC20080716-2010
                    ],
                    [
                        "id" => 17,
                        "title" => 'Here Comes the Rain Again',
                        "slug" => 'here-comes-the-rain-again',
                        'short_desc' => "Or there it goes. I can't remember now.",
                        'long_desc' => ''
                        // DSC20080716-2033-HDR
                    ],
                    [
                        "id" => 18,
                        "title" => 'Fall Color in My Backyard',
                        "slug" => 'fall-color-in-my-backyard',
                        'short_desc' => "Our little Japanese maple doing its thing.",
                        'long_desc' => ''
                        // DSC20091017-003
                    ],
                    [
                        "id" => 19,
                        "title" => 'A Sea of Green',
                        "slug" => 'a-sea-of-green',
                        'short_desc' => "Love the color contrast here, and I didn't have to force it at all.",
                        'long_desc' => ''
                        // DSC20091017-015
                    ],
                    [
                        "id" => 20,
                        "title" => 'Mirror Lake Bridge',
                        "slug" => 'mirror-lake-bridge',
                        'short_desc' => "A little bridge along the trail around the lake.",
                        'long_desc' => ''
                        // DSC20100704-042
                    ],
                    [
                        "id" => 21,
                        "title" => 'Sunset Near Bald Mountain',
                        "slug" => 'sunset-near-bald-mountain',
                        'short_desc' => "Lovely orange sky.",
                        'long_desc' => ''
                        // DSC20100814-119
                    ],
                    [
                        "id" => 22,
                        "title" => 'Sunset Above Bald Mountain',
                        "slug" => 'sunset-above-bald-mountain',
                        'short_desc' => "Everyone takes sunset photos of the horizon. I wanted to try something different.",
                        'long_desc' => ''
                        // DSC20100814-128
                    ]
                ]
            ],
            [
                "id" => 8,
                "photos" => [
                    [
                        "id" => 8,
                        "title" => 'Snow Peak',
                        "slug" => 'snow-peak',
                        'short_desc' => "A prominent feature of Banff.",
                        'long_desc' => ''
                        // DSC20060327-192
                    ],
                    [
                        "id" => 32,
                        "title" => 'Snow Peak 2',
                        "slug" => 'snow-peak-2',
                        'short_desc' => "Snow Peak stands above everything near Banff.",
                        'long_desc' => ''
                        // DSC20060327-194
                    ]

                ]
            ],
            [
                "id" => 14,
                "photos" => [
                    [
                        "id" => 2,
                        "title" => 'Quetzalcoatl',
                        "slug" => 'quetzalcoatl',
                        'short_desc' => 'Representation of the Feathered Serpent at Teotihuacan',
                        'long_desc' => ''
                        // DSC20070705-2823
                    ],
                    [
                        "id" => 3,
                        "title" => 'Temple of the Sun',
                        "slug" => 'temple-of-the-sun',
                        'short_desc' => 'Third largest pyramid in the world.',
                        'long_desc' => ''
                        // DSC20070705-2775
                    ],
                    [
                        "id" => 9,
                        "title" => 'Temple of the Moon',
                        "slug" => 'temple-of-the-moon',
                        'short_desc' => "A smaller temple near to the Temple of the Sun.",
                        'long_desc' => ''
                        // DSC20070705-2783
                    ]
                ]
            ],
            [
                "id" => 10,
                "photos" => [
                    [
                        'id' => 56,
                        'title' => 'A Blue Door',
                        'slug' => 'a-blue-door',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 57,
                        'title' => 'A Yellow Door',
                        'slug' => 'a-yellow-door',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 59,
                        'title' => 'Hobbits Love Yellow',
                        'slug' => 'hobbits-love-yellow',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 61,
                        'title' => 'Bag End',
                        'slug' => 'bag-end',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 66,
                        'title' => 'The Party Tree from The Green Dragon',
                        'slug' => 'the-party-tree-from-the-green-dragon',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ]
                ]
            ],
            [
                "id" => 11,
                "photos" => [
                    [
                        'id' => 82,
                        'title' => 'Milford Sound',
                        'slug' => 'milford-sound-90e5',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 83,
                        'title' => 'Milford Sound',
                        'slug' => 'milford-sound-46c5',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 84,
                        'title' => 'Milford Sound',
                        'slug' => 'milford-sound-a929',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 85,
                        'title' => 'Seals Chilling in Milford Sound',
                        'slug' => 'seals-chilling-in-milford-sound',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 167,
                        'title' => 'Falls in Milford Sound',
                        'slug' => 'falls-in-milford-sound',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ], [
                        'id' => 168,
                        'title' => 'Somewhere in Southland',
                        'slug' => 'somewhere-in-southland',
                        'short_desc' => NULL,
                        'long_desc' => NULL
                    ]
                ]
            ]
        );

        foreach ($galleries as $g) {
            $gallery = Gallery::find($g["id"]);
            foreach ($g['photos'] as $ph) {
                if (empty($ph['slug'])){
                    $ph['slug'] = str_slug($ph['title']);
                }
                $ph["canonical_gallery_id"] = $g["id"];
                $photo = new Photo($ph);
                $gallery->photos()->save($photo);
            }
        }
    }
}
