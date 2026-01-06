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
        // Ambil Customer yang status bank garansi-nya 'YA'
        $customers = Customer::where('bank_garansi', 'YA')->take(5)->get();

        if ($customers->count() < 5) {
            $this->command->error("Data Customer kurang dari 5! Jalankan 'php artisan db:seed --class=CustomerSeeder' terlebih dahulu.");
            return;
        }

        $currentYear = date('Y');
        $sequence = 1;

        $generateBgNumber = function() use (&$sequence, $currentYear) {
            $seqStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
            return "BG-{$currentYear}-{$seqStr}";
        };

        // Simpan ke variabel $c agar mudah diakses
        $c = $customers;

        // ==================================================================================
        // 1. SKENARIO UTAMA: AKAN EXPIRED (H-60) -> TARGET NOTIFIKASI
        // ==================================================================================
        // Syarat Notif: Status 'approved' DAN Expired Date antara Hari Ini s/d H+60
        BankGaransi::create([
            'customer_id' => $c[0]->id,
            'bg_number'   => $generateBgNumber(), // BG-2026-0001
            'bg_type'     => 'existing',
            'bg_nominal'  => 150000000,
            'issued_date' => Carbon::now()->subMonths(10),
            'exp_date'    => Carbon::now()->addDays(60)->format('Y-m-d'),
            'status'      => 'approved', 
            'created_by'  => 1
        ]);
        
        // ==================================================================================
        // 2. SKENARIO DARURAT: AKAN EXPIRED (H-10) -> TARGET NOTIFIKASI JUGA
        // ==================================================================================
        BankGaransi::create([
            'customer_id' => $c[1]->id,
            'bg_number'   => $generateBgNumber(), // BG-2026-0002
            'bg_type'     => 'existing',
            'bg_nominal'  => 50000000,
            'issued_date' => Carbon::now()->subMonths(11),
            'exp_date'    => Carbon::now()->addDays(10)->format('Y-m-d'),
            'status'      => 'approved',
            'created_by'  => 1
        ]);

        // ==================================================================================
        // 3. SKENARIO SUDAH EXPIRED (H-1 Kemarin)
        // ==================================================================================
        // Data ini TIDAK akan kena notif karena tanggalnya sudah lewat
        BankGaransi::create([
            'customer_id' => $c[2]->id,
            'bg_number'   => $generateBgNumber(), // BG-2026-0003
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
            'bg_number'   => $generateBgNumber(), // BG-2026-0004
            'bg_type'     => 'existing',
            'bg_nominal'  => 75000000,
            'issued_date' => Carbon::now()->subMonths(2),
            'exp_date'    => Carbon::now()->addDays(90)->format('Y-m-d'),
            'status'      => 'approved',
            'created_by'  => 1
        ]);

        // ==================================================================================
        // 5. SKENARIO LAIN: Expired H+59 (Juga Kena Notif)
        // ==================================================================================
        BankGaransi::create([
            'customer_id' => $c[4]->id,
            'bg_number'   => $generateBgNumber(), // BG-2026-0005
            'bg_type'     => 'existing',
            'bg_nominal'  => 20000000,
            'issued_date' => Carbon::now()->subMonths(5),
            'exp_date'    => Carbon::now()->addDays(59)->format('Y-m-d'),
            'status'      => 'approved',
            'created_by'  => 1
        ]);
    }
}