<?php

use Illuminate\Database\Seeder;

class PhotoTableSeeder extends Seeder
{
    public function run()
    {
        Photo::truncate();
        $photos = array(
            [
                "id" => 1,
                "title" => 'Fall Riot',
                "short_desc" => 'Flaming reds in the ground cover at BWCA',
                "long_desc" => 'Found this wandering around camp while canoeing in the BWCA. There was just something about the pattern in the small leaves and the contrast of the red against the green that drew my eye.'
                // DSC20041022-066
            ],
            [
                "id" => 2,
                "title" => 'Quetzalcoatl',
                'short_desc' => 'Representation of the Feathered Serpent at Teotihuacan',
                'long_desc' => ''
            ],
            [
                "id" => 3,
                "title" => 'Temple of the Sun',
                'short_desc' => 'Third largest pyramid in the world.',
                'long_desc' => ''
            ],
            [
                "id" => 4,
                "title" => 'Squirrel of Caerbannog',
                'short_desc' => "Fortunately I didn't have to use my Holy Hand Grenade of Antioch handy",
                'long_desc' => ''
            ],
            [
                "id" => 5,
                "title" => 'Doughnut Falls',
                'short_desc' => "",
                'long_desc' => ''
            ],
            [
                "id" => 6,
                "title" => 'Canoes Resting',
                'short_desc' => "Canoes pulled onto the shore after a day of paddling in the Boundary Waters.",
                'long_desc' => ''
            ],
            [
                "id" => 7,
                "title" => 'Paradox Valley',
                'short_desc' => "A hidden gem in southwest Colorado. Had no idea such a beautiful little valley existed.",
                'long_desc' => ''
            ],
            [
                "id" => 8,
                "title" => 'Snow Peak',
                'short_desc' => "A promminent feature of Banff.",
                'long_desc' => ''
            ]
        );
        foreach ($photos as $ph) {
            $ph['slug'] = strtolower(preg_replace('/\W/','_',$ph['title']));
            Photo::create($ph);
        }
    }
}