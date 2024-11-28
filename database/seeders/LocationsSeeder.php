<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\Location;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            'Manali',
            'Andaman',
            'Goa',
            'Kerala',
            'Ladakh',
            'Ooty',
            'Rajasthan',
            'Kashmir',
            'Darjeeling',
            'Coorg',
            'Gangtok',
            'Sikkim',
            'Himachal',
            'North East',
            'South India',
            'Bhubaneshwar',
            'Nainital',
            'Mahabaleshwar',
            'Shillong',
            'North India',
            'Agra',
            'Shirdi',
            'Mussoorie',
            'Shimla',
            'Uttarakhand',
            'Assam',
            'Munnar',
            'Jaisalmer',
            'Delhi',
            'Bangalore',
            'Hyderabad',
            'Udaipur',
            'Gujarat',
            'Chennai',
            'Mumbai',
            'Kullu',
            'Andaman And Nicobar',
            'Kullu Manali',
            'Shimla Manali',
            'Delhi Agra',
            'Mathura Vrindavan',
            'Leh Ladakh',
            'Jammu Kashmir',
            'Darjeeling Gangtok',
            'Chardham Yatra',
            'Auli',
            'Dalhousie',
            '12 Jyotirlinga',
        ];

        foreach ($locations as $location) {
            Location::create(['name' => $location]);
        }
    }
}
