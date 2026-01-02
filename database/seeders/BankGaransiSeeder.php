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
        $customers = Customer::where('bank_garansi', 'YA')->take(5)->get();

        if ($customers->count() < 5) {
            $this->command->error("Data Customer kurang dari 5! Jalankan 'php artisan db:seed --class=CustomerSeeder' terlebih dahulu.");
            return;
        }

        // Simpan ke variabel $c agar mudah diakses indeksnya ($c[0], $c[1], dst)
        $c = $customers;

        // ==================================================================================
        // 1. SKENARIO UTAMA: AKAN EXPIRED (H-60) -> TARGET NOTIFIKASI
        // ==================================================================================
        // Syarat Notif: Status 'approved' DAN Expired Date antara Hari Ini s/d H+60
        BankGaransi::create([
            'customer_id' => $c[0]->id,
            'bg_number'   => 'BG-TEST-NOTIF-H60',
            'bg_type'     => 'existing',
            'bg_nominal'  => 150000000,
            'issued_date' => Carbon::now()->subMonths(10),
            'exp_date'    => Carbon::now()->addDays(60)->format('Y-m-d'),
            'status'      => 'approved', // WAJIB APPROVED agar dianggap "Masih Aktif tapi Mau Expired"
            'created_by'  => 1
        ]);

        // ==================================================================================
        // 2. SKENARIO DARURAT: AKAN EXPIRED (H-10) -> TARGET NOTIFIKASI JUGA
        // ==================================================================================
        BankGaransi::create([
            'customer_id' => $c[1]->id,
            'bg_number'   => 'BG-TEST-URGENT-H10',
            'bg_type'     => 'existing',
            'bg_nominal'  => 50000000,
            'issued_date' => Carbon::now()->subMonths(11),
            'exp_date'    => Carbon::now()->addDays(10)->format('Y-m-d'), // 10 hari lagi expired
            'status'      => 'approved',
            'created_by'  => 1
        ]);

        // ==================================================================================
        // 3. SKENARIO SUDAH EXPIRED (H-1 Kemarin)
        // ==================================================================================
        // Data ini TIDAK akan kena notif "Akan Expired" karena tanggalnya sudah lewat
        BankGaransi::create([
            'customer_id' => $c[2]->id,
            'bg_number'   => 'BG-ALREADY-EXPIRED',
            'bg_type'     => 'existing',
            'bg_nominal'  => 250000000,
            'issued_date' => Carbon::now()->subMonths(12),
            'exp_date'    => Carbon::now()->subDays(1)->format('Y-m-d'),
            'status'      => 'expired',
            'created_by'  => 1
        ]);

        // ==================================================================================
        // 4. SKENARIO AMAN (Masih Lama H+90)
        // ==================================================================================
        // Data ini TIDAK akan kena notif karena > 60 hari
        BankGaransi::create([
            'customer_id' => $c[3]->id,
            'bg_number'   => 'BG-SAFE-H90',
            'bg_type'     => 'existing',
            'bg_nominal'  => 75000000,
            'issued_date' => Carbon::now()->subMonths(2),
            'exp_date'    => Carbon::now()->addDays(90)->format('Y-m-d'), // 90 hari lagi
            'status'      => 'approved',
            'created_by'  => 1
        ]);

        // ==================================================================================
        // 5. SKENARIO LAIN: Expired H+59
        // ==================================================================================
        BankGaransi::create([
            'customer_id' => $c[4]->id,
            'bg_number'   => 'BG-REQ-JOIN-REF',
            'bg_type'     => 'existing',
            'bg_nominal'  => 20000000,
            'issued_date' => Carbon::now()->subMonths(5),
            'exp_date'    => Carbon::now()->addDays(59)->format('Y-m-d'),
            'status'      => 'approved',
            'created_by'  => 1
        ]);
    }
}
