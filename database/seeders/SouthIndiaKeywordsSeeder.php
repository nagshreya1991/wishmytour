<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\Location;

class SouthIndiaKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "South India" location
        $northEastLocation = Location::firstOrCreate(['name' => 'South India']);

        // Define an array of places mapped to the "South India" location
        $places = [
            'Ooty',
            'Coorg',
            'Mysore',
            'Madurai',
            'Kodaikanal',
            'Rameshwaram',
            'Kanyakumari',
            'Wayanad',
            'Tirupati',
            'Bangalore',
            'Puducherry',
            'Kovalam And Poovar',
            'Mahabalipuram',
            'Munnar',
            'Bandipur',
            'Kabini',
            'Tanjore',
            'Thekkady',
            'Alleppey',
            'Chennai',
            'Chikmangalur',
            'Cochin',
            'Coonoor',
            'Gokarna',
            'Hyderabad',
            'Kanchipuram',
            'Kumbakonam',
            'Tiruchirappalli',
            'Trivandrum',
            'Vellore',
            'Mangalore',
            'Palakkad',
            'Varkala',
            'Velankanni'
        ];

        // Insert places into the location_keywords table
        foreach ($places as $place) {
            // Here, we create only the name as the keyword column is removed
            LocationKeyword::create([
                'location_id' => $northEastLocation->id,
                'name' => $place,
            ]);
        }
    }
}
