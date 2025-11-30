<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AllSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(ApprovalPathSeeder::class); 
        $this->call(ItemSeeder::class);
    }
}