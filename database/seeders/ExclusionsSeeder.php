<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Package\app\Models\Exclusion;

class ExclusionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $values = [
            '5% GST',
            'Any Airfare / Train fare',
            'Camera fee',
            'Alcoholic / Non-Alcoholic beverages',
            'Travel insurance',
            'Expenses caused by factors beyond our control like rail and flight delays, roadblocks, vehicle mal-functions, political disturbances etc.',
            'Tips, laundry & phone call',
            'Entrance fees to monuments and museum',
            'Entry fees at monuments and guide services',
            'All personal expenses',
            'Any expenses of personal nature',
            'Expenses of personal nature like medical expenses, phone calls, etc.',
            'Tips, laundry & phone call',
            'Nathula Pass',
            'Rohtang pass',
        ];

        foreach ($values as $value) {
            Exclusion::create(['name' => $value]);
        }
    }
}
