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
				"id" => 19,
				"photos" => [
					[
						"id" => 1,
						"title" => 'Fall Riot',
						"short_desc" => 'Flaming reds in the ground cover at BWCA',
						"long_desc" => 'Found this wandering around camp while canoeing in the BWCA. There was just something about the pattern in the small leaves and the contrast of the red against the green that drew my eye.'
						// DSC20041022-066
					],
					[
						"id" => 24,
						"title" => 'Making Camp',
						'short_desc' => "Canoes pulled out of the water after a day of paddling.",
						'long_desc' => ''
						// DSC20041017-011
					],
					[
						"id" => 6,
						"title" => 'Canoes Resting',
						'short_desc' => "Canoes waiting on shore while we eat some lunch.",
						'long_desc' => ''
						// DSC20041018-020
					],
					[
						"id" => 23,
						"title" => 'Mirrored Sunset',
						'short_desc' => "Sunset during one of my trips to the Boundary Waters.",
						'long_desc' => ''
						// DSC20091020-43
					]
				]
			],
			[
				"id" => 14,
				"photos" => [
					[
						"id" => 2,
						"title" => 'Quetzalcoatl',
						'short_desc' => 'Representation of the Feathered Serpent at Teotihuacan',
						'long_desc' => ''
						// DSC20070705-2823
					],
					[
						"id" => 3,
						"title" => 'Temple of the Sun',
						'short_desc' => 'Third largest pyramid in the world.',
						'long_desc' => ''
						// DSC20070705-2775
					],
					[
						"id" => 9,
						"title" => 'Temple of the Moon',
						'short_desc' => "A smaller temple near to the Temple of the Sun.",
						'long_desc' => ''
						// DSC20070705-2793
					]
				]
			],
			[
				"id" => 5,
				"photos" => [
					[
						"id" => 4,
						"title" => 'Squirrel of Caerbannog',
						'short_desc' => "Fortunately I didn't have to use my Holy Hand Grenade of Antioch",
						'long_desc' => ''
						// DSC20030729-007
					],
					[
						"id" => 5,
						"title" => 'Doughnut Falls',
						'short_desc' => "",
						'long_desc' => ''
						// DSC20040705-009
					],
					[
						"id" => 10,
						"title" => 'Bright Yellow',
						'short_desc' => "A blazing arc of aspens in the Utah mountains.",
						'long_desc' => ''
						// DSC20021027-028
					],
					[
						"id" => 11,
						"title" => 'Mallard Noir',
						'short_desc' => "I liked the composition, but it wasn't much of a photo. Heavy post processing later...voila.",
						'long_desc' => ''
						// DSC20030729-001
					],
					[
						"id" => 12,
						"title" => 'Lone Peak',
						'short_desc' => "It may be Spring in the valley, but there is plenty of snow still in the mountains in May.",
						'long_desc' => ''
						// DSC20050514-432
					],
					[
						"id" => 13,
						"title" => 'Aspens in Spring',
						'short_desc' => "Not sure what it is about aspens that is so mesmerizing.",
						'long_desc' => ''
						// DSC20050522-470
					],
					[
						"id" => 14,
						"title" => 'Mount Timpanogos',
						'short_desc' => "Mt. Timpanogos is a prominent feature of Utah County and, like any mountain peak, has many moods.",
						'long_desc' => ''
						// DSC20050522-470
					],
					[
						"id" => 15,
						"title" => 'Water falling near Lake Blanche',
						'short_desc' => "The date on the photo says March. That must mean we had almost no snow that year.",
						'long_desc' => ''
						// DSC20080302-060
					],
					[
						"id" => 16,
						"title" => 'Red rock near Moab',
						'short_desc' => "This canyon south of Moab accessible only by dirt road...or mountain bike.",
						'long_desc' => ''
						// DSC20080716-2010
					],
					[
						"id" => 17,
						"title" => 'Here Comes the Rain Again',
						'short_desc' => "Or there it goes. I can't remember now.",
						'long_desc' => ''
						// DSC20080716-2033-HDR
					],
					[
						"id" => 18,
						"title" => 'Fall Color in My Backyard',
						'short_desc' => "Our little Japanese maple doing its thing.",
						'long_desc' => ''
						// DSC20091017-003
					],
					[
						"id" => 19,
						"title" => 'A Sea of Green',
						'short_desc' => "Love the color contrast here, and I didn't have to force it at all.",
						'long_desc' => ''
						// DSC20091017-015
					],
					[
						"id" => 20,
						"title" => 'Mirror Lake Bridge',
						'short_desc' => "A little bridge along the trail around the lake.",
						'long_desc' => ''
						// DSC20100704-042
					],
					[
						"id" => 21,
						"title" => 'Sunset Near Bald Mountain',
						'short_desc' => "Lovely orange sky.",
						'long_desc' => ''
						// DSC20091017-003
					],
					[
						"id" => 22,
						"title" => 'Sunset Above Bald Mountain',
						'short_desc' => "Everyone takes sunset photos of the horizon. I wanted to try something different.",
						'long_desc' => ''
						// DSC20091017-003
					]
				]
			],
			[
				"id" => 4,
				"photos" => [
					[
						"id" => 7,
						"title" => 'Paradox Valley',
						'short_desc' => "A hidden gem in southwest Colorado. Had no idea such a beautiful little valley existed.",
						'long_desc' => ''
						// 20090719-153
					],
					[
						"id" => 30,
						"title" => 'Ute Canyon, Colorado National Monument',
						'short_desc' => "Ute Canyon overlook on the south end of the park.",
						'long_desc' => ''
						// 20090619-DSC090619_031
					],
					[
						"id" => 31,
						"title" => 'Sunset over McPhee Reservoir',
						'short_desc' => "Just an amazing summer sky at sunset.",
						'long_desc' => ''
						// DSC20090713-123
					]
				]
			],
			[
				"id" => 6,
				"photos" => [
					[
						"id" => 25,
						"title" => 'Sunbathing',
						'short_desc' => "A little guy soaking up some sun.",
						'long_desc' => ''
						// DSC20090327-022
					],
					[
						"id" => 26,
						"title" => 'Sky on Fire',
						'short_desc' => "A tricky shot that took a fair amount of work in Photoshop to get right.",
						'long_desc' => ''
						// DSC20090327-023
					],
					[
						"id" => 27,
						"title" => 'Big Ball of Fire',
						'short_desc' => "Sunrise on the water.",
						'long_desc' => ''
						// DSC20090327-023
					],
					[
						"id" => 28,
						"title" => 'Kayaking in the Florida Keys',
						'short_desc' => "Stopping on a small island for a look-see.",
						'long_desc' => ''
						// DSC20090327-023
					],
					[
						"id" => 29,
						"title" => 'Too Windy to Fly',
						'short_desc' => "Terns waiting for the wind to settle down.",
						'long_desc' => ''
						// DSC20090327-023
					]
				]
			],
			[
				"id" => 8,
				"photos" => [
					[
						"id" => 8,
						"title" => 'Snow Peak',
						'short_desc' => "A prominent feature of Banff.",
						'long_desc' => ''
						// DSC20060327-192
					],
					[
						"id" => 32,
						"title" => 'Snow Peak 2',
						'short_desc' => "Snow Peak stands above everything near Banff.",
						'long_desc' => ''
						// DSC20060327-195
					]

				]
			]
		);

        foreach ($galleries as $g) {
			$gallery = Gallery::find($g["id"]);
			foreach ($g['photos'] as $ph) {
				$ph['slug'] = str_slug($ph['title']);
				$photo = new Photo($ph);
				$gallery->photos()->save($photo);
			}
        }
    }
}