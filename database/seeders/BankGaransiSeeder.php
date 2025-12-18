<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use Carbon\Carbon;

class BankGaransiSeeder extends Seeder
{
    public function run()
    {
        // Ambil 5 Customer yang sudah disiapkan
        $customers = [];
        for ($i = 1; $i <= 5; $i++) {
            $c = Customer::where('email', "customer.sejahtera{$i}@example.com")->first();
            if ($c) $customers[] = $c;
        }

        if (count($customers) < 5) {
            $this->command->error("Jalankan CustomerSeeder dulu!");
            return;
        }

        // 1. Skenario H-60 dari HARI INI (Untuk tes trigger notifikasi yang benar)
        // Expired: Hari ini + 60 hari
        BankGaransi::create([
            'customer_id' => $customers[0]->id,
            'bg_number' => 'BG-TEST-TRIGGER-60',
            'bg_type' => 'existing',
            'bg_nominal' => 100000000,
            'issued_date' => Carbon::now()->subMonths(10),
            'exp_date' => Carbon::now()->addDays(60)->format('Y-m-d'), // PENTING: Ini yang akan kena trigger H-60
            'status' => 'approved', // Status harus approved biar kebaca sistem
            'created_by' => 1
        ]);

        // 2. Skenario H-30 dari HARI INI
        BankGaransi::create([
            'customer_id' => $customers[1]->id,
            'bg_number' => 'BG-TEST-TRIGGER-30',
            'bg_type' => 'existing',
            'bg_nominal' => 50000000,
            'issued_date' => Carbon::now()->subMonths(6),
            'exp_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'status' => 'approved',
            'created_by' => 1
        ]);

        // 3. Skenario SUDAH EXPIRED (H-10 kemarin)
        BankGaransi::create([
            'customer_id' => $customers[2]->id,
            'bg_number' => 'BG-TEST-EXPIRED',
            'bg_type' => 'existing',
            'bg_nominal' => 250000000,
            'issued_date' => Carbon::now()->subMonths(12),
            'exp_date' => Carbon::now()->subDays(10)->format('Y-m-d'), 
            'status' => 'expired',
            'created_by' => 1
        ]);

        // 4. Skenario AMAN (Masih 90 hari lagi)
        BankGaransi::create([
            'customer_id' => $customers[3]->id,
            'bg_number' => 'BG-TEST-AMAN',
            'bg_type' => 'existing',
            'bg_nominal' => 75000000,
            'issued_date' => Carbon::now()->subMonths(2),
            'exp_date' => Carbon::now()->addDays(90)->format('Y-m-d'),
            'status' => 'approved',
            'created_by' => 1
        ]);

        // 5. SKENARIO REQUEST KHUSUS: Expired H+60 DARI JOIN DATE
        // Misal Join Date: 1 Januari 2024 -> Expired: 1 Maret 2024
        // (Data ini kemungkinan besar statusnya sudah Expired karena Join Date di CustomerSeeder pakai subYears)
        $joinDateCust5 = Carbon::parse($customers[4]->join_date);
        
        BankGaransi::create([
            'customer_id' => $customers[4]->id,
            'bg_number' => 'BG-REQ-JOIN-60',
            'bg_type' => 'existing',
            'bg_nominal' => 20000000,
            'issued_date' => $joinDateCust5, // Terbit pas join
            'exp_date' => $joinDateCust5->copy()->addDays(60)->format('Y-m-d'), // Expired 60 hari setelah join
            'status' => 'expired', // Kemungkinan besar sudah expired
            'created_by' => 1
        ]);
    }
}