<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\Inclusion;

class InclusionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [
            '01 nights accommodation at mentioned hotels as per meal plans mentioned',
            'Road Taxes and Parking charges',
            'All transfers and sightseeing-using vehicle as mentioned',
            'Welcome drink on arrival',
            'Wi-Fi',
            'Parking and Toll tax',
            'Meet & greet at arrival',
            'Breakfast',
            'Dinner',
            'Pick and Drop at time of arrival/departure',
            "Driver's allowance, Road tax and Fuel charges",
            'Sightseeing by private car',
            'All tours and transfers by Personal Car is included',
            'Road Taxes and Parking charges',
            'Inner Line Permit Charges for visit to Arunachal Pradesh',
            'Bus',
            'Tea/Coffee Kettle in the Room',
            'Inter island cruise tickets will be provided on Green Ocean/Makruzz/Express Bhagya (Govt Ferry will be provided if pvt cruise does not operate',
            'All rooms with CP (Breakfast)',
            'All Transfers and sightseeing on Pvt Cabs Xylo or Ertiga ( Small cars at Neil island as per availability)',
            'All Permits and Entry tickets as per itinerary',
        ];

        foreach ($values as $value) {
            Inclusion::create(['name' => $value]);
        }
    }
}
