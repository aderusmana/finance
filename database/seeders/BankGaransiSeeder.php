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

        $currentYear = date('Y');
        $sequence = 1;

        $generateBgNumber = function() use (&$sequence, $currentYear) {
            $seqStr = str_pad($sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
            return "BG-{$currentYear}-{$seqStr}";
        };

        $c = $customers;

        BankGaransi::create([
            'customer_id' => $c[0]->id,
            'bg_number'   => $generateBgNumber(), // BG-2026-0001
            'bg_type'     => 'new',
            'base_bg_id'  => 1,
            'bg_nominal'  => 150000000,
            'issued_date' => Carbon::now()->subMonths(10),
            'exp_date'    => Carbon::now()->addDays(60)->format('Y-m-d'),
            'status'      => 'approved',
            'created_by'  => 1
        ]);

        // BankGaransi::create([
        //     'customer_id' => $c[1]->id,
        //     'bg_number'   => $generateBgNumber(), // BG-2026-0002
        //     'bg_type'     => 'new',
        //     'bg_nominal'  => 50000000,
        //     'issued_date' => Carbon::now()->subMonths(11),
        //     'exp_date'    => Carbon::now()->addDays(10)->format('Y-m-d'),
        //     'status'      => 'approved',
        //     'created_by'  => 1
        // ]);

        // BankGaransi::create([
        //     'customer_id' => $c[2]->id,
        //     'bg_number'   => $generateBgNumber(), // BG-2026-0003
        //     'bg_type'     => 'new',
        //     'bg_nominal'  => 250000000,
        //     'issued_date' => Carbon::now()->subMonths(12),
        //     'exp_date'    => Carbon::now()->subDays(1)->format('Y-m-d'),
        //     'status'      => 'expired',
        //     'created_by'  => 1
        // ]);

        // BankGaransi::create([
        //     'customer_id' => $c[3]->id,
        //     'bg_number'   => $generateBgNumber(), // BG-2026-0004
        //     'bg_type'     => 'new',
        //     'bg_nominal'  => 75000000,
        //     'issued_date' => Carbon::now()->subMonths(2),
        //     'exp_date'    => Carbon::now()->addDays(90)->format('Y-m-d'),
        //     'status'      => 'approved',
        //     'created_by'  => 1
        // ]);

        // BankGaransi::create([
        //     'customer_id' => $c[4]->id,
        //     'bg_number'   => $generateBgNumber(),
        //     'bg_type'     => 'new',
        //     'bg_nominal'  => 20000000,
        //     'issued_date' => Carbon::now()->subMonths(5),
        //     'exp_date'    => Carbon::now()->addDays(59)->format('Y-m-d'),
        //     'status'      => 'approved',
        //     'created_by'  => 1
        // ]);
    }
}
