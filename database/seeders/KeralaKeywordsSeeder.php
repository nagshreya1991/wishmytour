<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\Location;

class KeralaKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "Kerala" location
        $keralaLocation = Location::firstOrCreate(['name' => 'Kerala']);

        // Define an array of places mapped to the "Kerala" location
        $places = [
            'Munnar',
            'Alleppey',
            'Thekkady',
            'Kovalam And Poovar',
            'Cochin',
            'Wayanad',
            'Kanyakumari',
            'Madurai',
            'Rameshwaram',
            'Kumarakom',
            'Ooty',
            'Coorg',
            'Mysore',
            'Trivandrum',
            'Kabini',
            'Varkala',
            'Kodaikanal',
            'Athirappilly',
            'Vagamon',
            'Bangalore',
            'Calicut',
            'Guruvayoor',
            'Agatti',
            'Bekal',
            'Palakkad',
            'Velankanni'
        ];

        // Insert places into the location_keywords table
        foreach ($places as $place) {
            // Here, we create only the name as the keyword column is removed
            LocationKeyword::create([
                'location_id' => $keralaLocation->id,
                'name' => $place,
            ]);
        }
    }
}
