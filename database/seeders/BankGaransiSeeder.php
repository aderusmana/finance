<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer; // Asumsi ada model Customer
use Carbon\Carbon;

class BankGaransiSeeder extends Seeder
{
    public function run()
    {
        // Create 5 dummy customers
        $customers = [];
        for ($i = 1; $i <= 5; $i++) {
            $customers[] = Customer::create([
                'name' => "PT. Customer {$i}",
                'code' => 'CUST00' . $i,
                'email' => "customer{$i}@example.com"
            ]);
        }

        $customer = $customers[0];

        // Buat BG yang akan expired TEPAT 60 hari dari sekarang
        BankGaransi::create([
            'customer_id' => $customer->id,
            'bg_number' => 'BG-TEST-60DAYS',
            'bg_type' => 'existing',
            'bg_nominal' => 100000000,
            'issued_date' => Carbon::now()->subYear(),
            'exp_date' => Carbon::now()->addDays(60)->format('Y-m-d'),
            // 'exp_date' => Carbon::now()->addDays(60),
            'status' => 'approved',
            'created_by' => 1
        ]);

        // Buat BG lain (aman)
        BankGaransi::create([
            'customer_id' => $customer->id,
            'bg_number' => 'BG-AMAN',
            'bg_type' => 'existing',
            'bg_nominal' => 50000000,
            'issued_date' => Carbon::now()->subMonths(6),
            'exp_date' => Carbon::now()->addDays(60)->format('Y-m-d'),
            // 'exp_date' => Carbon::now()->addDays(120),
            'status' => 'approved',
            'created_by' => 1
        ]);

        BankGaransi::create([
            'customer_id' => $customer->id,
            'bg_number' => 'BG-JAYA-AMAN',
            'bg_type' => 'existing',
            'bg_nominal' => 50000000,
            'issued_date' => Carbon::now()->subMonths(6),
            'exp_date' => Carbon::now()->addDays(120),
            'status' => 'approved',
            'created_by' => 1
        ]);

        // Buat BG lain (sudah expired)
        BankGaransi::create([
            'customer_id' => $customer->id,
            'bg_number' => 'BG-EXPIRED',
            'bg_type' => 'existing',
            'bg_nominal' => 75000000,
            'issued_date' => Carbon::now()->subYears(2),
            'exp_date' => Carbon::now()->subDays(10),
            'status' => 'expired',
            'created_by' => 1
        ]);

        // Tambah BG lain untuk customer lain
        $otherCustomer = $customers[1];
        BankGaransi::create([
            'customer_id' => $otherCustomer->id,
            'bg_number' => 'BG-OTHER-CUST',
            'bg_type' => 'existing',
            'bg_nominal' => 20000000,
            'issued_date' => Carbon::now()->subMonths(3),
            'exp_date' => Carbon::now()->addDays(90),
            'status' => 'approved',
            'created_by' => 1
        ]);
    }
}
