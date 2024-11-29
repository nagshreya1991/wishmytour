<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\Location;

class GangtokKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "Gangtok" location
        $gangtokLocation = Location::firstOrCreate(['name' => 'Gangtok']);

        // Define an array of places mapped to the "Gangtok" location
        $places = [
            'Gangtok',
            'Gangtok',
            'Pelling',
            'Lachung',
            'Kalimpong',
            'Lachen',
            'Namchi',
            'Aritar'
        ];

        // Insert places into the location_keywords table
        foreach ($places as $place) {
            // Here, we create only the name as the keyword column is removed
            LocationKeyword::create([
                'location_id' => $gangtokLocation->id,
                'name' => $place,
            ]);
        }
    }
}
