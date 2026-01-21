<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BG\Tax; // Pastikan import Model Tax

class TaxsTableSeeder extends Seeder
{
    public function run()
    {
        
        Tax::updateOrCreate(
            ['name' => '11%'],
            [
                'value' => 0.11,
            ]
        );
        
    }
}