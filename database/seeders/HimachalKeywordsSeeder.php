<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\LocationKeyword;
use Modules\Package\app\Models\Location;

class HimachalKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "Himachal" location
        $northEastLocation = Location::firstOrCreate(['name' => 'Himachal']);

        // Define an array of places mapped to the "Himachal" location
        $places = [
            'Manali',
            'Shimla',
            'Dharamshala',
            'Chandigarh',
            'Jibhi',
            'Kasol',
            'Amritsar',
            'Leh',
            'Nubra Valley',
            'Pangong',
            'Sarchu',
            'Dalhousie',
            'Kullu',
            'Kargil',
            'Srinagar',
            'Kasauli',
            'Theog',
            'Dehradun',
            'Hanle',
            'Kufri',
            'Mussoorie',
            'Sangla',
            'Katra',
            'New Delhi',
            'Chail',
            'Chamba Hp',
            'Chandrataal',
            'Chintpurni',
            'Kalpa',
            'Kangra',
            'Solan',
            'Tsomoriri'
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
