<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('bg_taxs')->truncate();

        DB::table('bg_taxs')->insert([
            'name' => '11%',
            'value' => '0.11',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
