<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\Location;

class KashmirKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "Kashmir" location
        $kashmirLocation = Location::firstOrCreate(['name' => 'Kashmir']);

        // Define an array of places mapped to the "Kashmir" location
        $places = [
            'Srinagar',
            'Pahalgam',
            'Gulmarg',
            'Katra',
            'Srinagar Houseboat',
            'Sonmarg',
            'Kargil',
            'Leh',
            'Nubra Valley',
            'Pangong',
            'Manali',
            'Patnitop',
            'Sarchu',
            'Amritsar',
            'Jammu',
            'Dharamshala',
            'Chandigarh',
            'Chintpurni',
            'Kangra',
            'Tsomoriri'
        ];

        // Insert places into the location_keywords table
        foreach ($places as $place) {
            // Here, we create only the name as the keyword column is removed
            LocationKeyword::create([
                'location_id' => $kashmirLocation->id,
                'name' => $place,
            ]);
        }
    }
}
