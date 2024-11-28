<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\RegionCity;
use Modules\Package\app\Models\Region;
use Modules\Package\app\Models\City;

class NorthEastKeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Find or create the "North East" location
        $northEastRegion = Region::firstOrCreate(['name' => 'North East']);

        // Define an array of places mapped to the "North East" location
        $places = [
            'Gangtok',
            'Darjeeling',
            'Pelling',
            'Shillong',
            'Guwahati',
            'Lachung',
            'Kaziranga',
            'Kalimpong',
            'Cherrapunjee',
            'Lachen',
            'Namchi',
            'Nameri',
            'Aritar',
            'Bomdila',
            'Dirang',
            'Manas',
            'Tawang',
            'Tezpur',
            'Dooars',
            'Kolkata',
            'Siliguri',
        ];

        // Insert places into the location_keywords table
        foreach ($places as $place) {
            // Find the city by name
            $city = City::where('city', $place)->first();

            if ($city) {
                // Here, we create only the name as the keyword column is removed
                RegionCity::create([
                    'region_id' => $northEastRegion->id,
                    'city_id' => $city->id,
                    'name' => $place,
                ]);
            } else {
                // If city not found, you can handle it as per your requirement
                echo "City '$place' not found. Skipping...\n";
            }
        }
    }
}
