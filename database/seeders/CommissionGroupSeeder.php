<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('commission_groups')->insert([
            [
                'name' => 'Gold',
                'commission' => 10.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Silver',
                'commission' => 5.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Bronze',
                'commission' => 2.50,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
