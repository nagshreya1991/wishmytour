<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\TourismCircuit;

class TourismCircuitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $circuits = [
            'The Golden Circuit',
            'Nilgiri Circuit',
            'Backwaters Circuit',
            'Chota Char Dham Circuit',
            'Sufi Circuit',
            'Christian Circuit',
            'Tirthankara Circuit',
            'Buddhist Circuit',
            'Wildlife Circuit',
            'Tribal Circuit',
            'Rural Circuit',
            'Heritage Circuit',
            'Desert Circuit',
            'Coastal Circuit',
            'Himalayan Circuit',
            'North-East Circuit',
            'Eco-Tourism Circuit',
            'Ramayana Circuit',
            'Krishna Circuit',
            'Spiritual Circuit',
        ];

        foreach ($circuits as $circuit) {
            TourismCircuit::create(['name' => $circuit]);
        }
    }
}
