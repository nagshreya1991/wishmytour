<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\Location;

class DarjeelingKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "Darjeeling" location
        $darjeelingLocation = Location::firstOrCreate(['name' => 'Darjeeling']);

        // Define an array of places mapped to the "Darjeeling" location
        $places = [
            'Darjeeling',
            'Gangtok',
            'Pelling',
            'Lachung',
            'Kalimpong',
            'Lachen',
            'Namchi',
            'Siliguri'
        ];

        // Insert places into the location_keywords table
        foreach ($places as $place) {
            // Here, we create only the name as the keyword column is removed
            LocationKeyword::create([
                'location_id' => $darjeelingLocation->id,
                'name' => $place,
            ]);
        }
    }
}
