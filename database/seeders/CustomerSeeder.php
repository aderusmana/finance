<?php

namespace Database\Seeders;

use App\Models\Master\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory()->count(20)->create();
    }
}
