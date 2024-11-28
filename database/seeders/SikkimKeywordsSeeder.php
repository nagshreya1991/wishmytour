<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\Location;

class SikkimKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "Sikkim" location
        $northEastLocation = Location::firstOrCreate(['name' => 'Sikkim']);

        // Define an array of places mapped to the "Sikkim" location
        $places = [
            'Gangtok',
            'Darjeeling',
            'Pelling',
            'Lachung',
            'Kalimpong',
            'Lachen',
            'Namchi',
            'Aritar',
            'Siliguri'
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
