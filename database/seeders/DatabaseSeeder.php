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
        $this->call(MasterManagementSeeder::class);
        $this->call(BankGaransiSeeder::class);
        $this->call(TaxsTableSeeder::class);
        $this->call(BGLimitRulesSeeder::class);
        $this->call(BgLampiranDApprovalSeeder::class);
        $this->call(CustomerApprovalSeeder::class);
        $this->call(DistributorSeeder::class);
    }
}
