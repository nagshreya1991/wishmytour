<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\Location;

class LadakhKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "Ladakh" location
        $ladakhLocation = Location::firstOrCreate(['name' => 'Ladakh']);

        // Define an array of places mapped to the "Ladakh" location
        $places = [
            'Leh',
            'Nubra Valley',
            'Pangong',
            'Manali',
            'Sarchu',
            'Kargil',
            'Srinagar',
            'Tsomoriri',
            'Hanle'
        ];

        // Insert places into the location_keywords table
        foreach ($places as $place) {
            // Here, we create only the name as the keyword column is removed
            LocationKeyword::create([
                'location_id' => $ladakhLocation->id,
                'name' => $place,
            ]);
        }
    }
}
