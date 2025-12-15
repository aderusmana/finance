<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TextsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('texts')->insert([
            'name' => 'increase_percentage',
            'value' => '11', // 11%
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
