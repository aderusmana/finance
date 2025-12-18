<?php

namespace Database\Seeders;

use App\Models\Customer\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 5 Customer Spesifik untuk Testing
        for ($i = 1; $i <= 5; $i++) {
            Customer::firstOrCreate(
                ['email' => "customer.sejahtera{$i}@example.com"],
                [
                    'name' => "PT. Customer Sejahtera {$i}",
                    'code' => 'CUST' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'term_of_payment' => 30,       
                    'lead_time' => 7,
                    'join_date' => now()->subYears(rand(1, 5)),
                ]
            );
        }
    }
}