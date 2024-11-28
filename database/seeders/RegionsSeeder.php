<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\Region;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            'North East',
            'South India',
            'North India',
            'East India',
            'North East India'
        ];

        foreach ($regions as $region) {
            Region::create(['name' => $region]);
        }
    }
}
