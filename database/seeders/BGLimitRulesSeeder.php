<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BGLimitRulesSeeder extends Seeder
{
    public function run()
    {
        DB::table('bg_limit_rules')->truncate();

        $data = [
            [
                'min_year' => 0,
                'max_year' => 2,
                'percentage' => 10.00, // 10%
                'description' => 'Customer baru (0-2 Tahun)',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'min_year' => 3,
                'max_year' => 5,
                'percentage' => 15.00, // 15%
                'description' => 'Customer menengah (3-5 Tahun)',
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'min_year' => 6,
                'max_year' => 100, // Unlimited
                'percentage' => 20.00, // 20%
                'description' => 'Customer loyal (> 5 Tahun)',
                'created_at' => now(), 'updated_at' => now()
            ],
        ];

        DB::table('bg_limit_rules')->insert($data);
    }
}
