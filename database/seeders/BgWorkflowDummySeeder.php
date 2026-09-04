<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer\Customer;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgDetail;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BgPeriod;
use App\Models\BG\BgSubmission;
use App\Models\BG\LampiranD;
use App\Models\BG\LampiranDVersion;
use App\Models\BG\BgHistory;
use App\Models\BG\Tax;
use App\Models\BG\BgLimitRule;
use App\Models\Master\ApprovalPath;
use App\Models\Master\ApprovalLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class BgWorkflowDummySeeder extends Seeder
{
    public function run()
    {
        $this->command->info("Memulai pembuatan data dummy Bank Garansi (BG) EXPIRED TODAY...");

        // 1. Bersihkan placeholder dummy jika ada
        $placeholderCustomerIds = Customer::where('code', 'LIKE', 'CUST-BG-%')->pluck('id');
        if ($placeholderCustomerIds->isNotEmpty()) {
            BankGaransi::whereIn('customer_id', $placeholderCustomerIds)->delete();
            BgRecommendation::whereIn('customer_id', $placeholderCustomerIds)->delete();
            Customer::whereIn('id', $placeholderCustomerIds)->delete();
        }

        // Helper untuk validasi email
        $getValidEmail = function($cust) {
            if (!empty($cust->email) && filter_var($cust->email, FILTER_VALIDATE_EMAIL)) {
                return $cust->email;
            }
            return 'distributor.' . Str::slug($cust->name) . '@example.com';
        };

        // 2. Pastikan Konfigurasi Tax & Limit Rules Tersedia
        $tax = Tax::firstOrCreate(
            ['id' => 1],
            ['name' => 'PPN', 'value' => 0.11]
        );

        if (BgLimitRule::count() === 0) {
            BgLimitRule::insert([
                ['min_year' => 0, 'max_year' => 2, 'percentage' => 10.00, 'description' => 'Customer baru (0-2 Tahun)', 'created_at' => now(), 'updated_at' => now()],
                ['min_year' => 3, 'max_year' => 5, 'percentage' => 15.00, 'description' => 'Customer menengah (3-5 Tahun)', 'created_at' => now(), 'updated_at' => now()],
                ['min_year' => 6, 'max_year' => 100, 'percentage' => 20.00, 'description' => 'Customer loyal (> 5 Tahun)', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 3. Pastikan Role Tersedia
        if (!Role::where('name', 'manager-finance')->exists()) {
            Role::create(['name' => 'manager-finance', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'admin-rtm')->exists()) {
            Role::create(['name' => 'admin-rtm', 'guard_name' => 'web']);
        }

        // Ambil admin user dan assign role admin-rtm agar notifikasi berjalan
        $adminUser = User::role('super-admin')->first() ?? User::first();
        if ($adminUser && !$adminUser->hasRole('admin-rtm')) {
            $adminUser->assignRole('admin-rtm');
        }

        // 4. Ambil 2 Customer Asli untuk dibuatkan BG Expired Today
        $customers = Customer::orderBy('id', 'asc')->take(2)->get();
        if ($customers->isEmpty()) {
            $this->command->error("Tabel customers masih kosong! Silakan seed customer terlebih dahulu.");
            return;
        }

        $now = Carbon::now();
        $todayStr = $now->format('Y-m-d');
        $createdBgs = [];

        foreach ($customers as $index => $cust) {
            // Update customer menjadi aktif & punya BG
            $cust->update([
                'bank_garansi'          => 'YA',
                'term_of_payment'       => $cust->term_of_payment ?: 30,
                'lead_time'             => $cust->lead_time ?: 7,
                'join_date'             => $cust->join_date ?: $now->copy()->subYears(3)->format('Y-m-d'),
                'area'                  => $cust->area && $cust->area !== '-' ? $cust->area : 'Jawa Barat',
                'city'                  => $cust->city ?: 'Bandung',
                'email'                 => $getValidEmail($cust),
                'status'                => 'active',
                'status_approval'       => 'approved',
                'approved_credit_limit' => 1500000000,
            ]);

            // Bersihkan data BG, rekomendasi lama, atau submission lama untuk customer ini
            $oldRecIds = BgRecommendation::where('customer_id', $cust->id)->pluck('id');
            if ($oldRecIds->isNotEmpty()) {
                BgPeriod::whereIn('bg_recommendation_id', $oldRecIds)->delete();
                $subIds = BgSubmission::whereIn('bg_recommendation_id', $oldRecIds)->pluck('id');
                if ($subIds->isNotEmpty()) {
                    $lampiranIds = LampiranD::whereIn('bg_submission_id', $subIds)->pluck('id');
                    LampiranDVersion::whereIn('lampiran_d_id', $lampiranIds)->delete();
                    LampiranD::whereIn('id', $lampiranIds)->delete();
                    ApprovalLog::where('category', 'BG')->whereIn('related_id', $subIds)->delete();
                    BgSubmission::whereIn('id', $subIds)->delete();
                }
                BgRecommendation::whereIn('id', $oldRecIds)->delete();
            }

            $oldBgIds = BankGaransi::where('customer_id', $cust->id)->pluck('id');
            if ($oldBgIds->isNotEmpty()) {
                BgDetail::whereIn('bank_garansi_id', $oldBgIds)->delete();
                BgHistory::whereIn('bank_garansi_id', $oldBgIds)->delete();
                BankGaransi::whereIn('id', $oldBgIds)->delete();
            }

            // Nominal BG dummy
            $nominal = 350000000 + ($index * 150000000);
            $bgNumber = 'BG-' . $now->year . '-000' . ($index + 1);

            // Buat Bank Garansi dengan status APPROVED dan EXP_DATE = HARI INI
            $bg = BankGaransi::create([
                'customer_id' => $cust->id,
                'bg_number'   => $bgNumber,
                'bg_type'     => 'new',
                'bg_nominal'  => $nominal,
                'issued_date' => $now->copy()->subYear()->format('Y-m-d'),
                'exp_date'    => $todayStr, // EXPIRED TODAY!
                'status'      => 'approved',
                'created_by'  => $adminUser->id ?? 1,
                'created_at'  => $now->copy()->subYear(),
                'updated_at'  => $now,
            ]);
            $bg->update(['base_bg_id' => $bg->id]);

            // Buat detail bank
            BgDetail::create([
                'bank_garansi_id' => $bg->id,
                'bank_name'       => $index === 0 ? 'Bank Central Asia (BCA)' : 'Bank Mandiri',
                'branch_name'     => $index === 0 ? 'KCU Sudirman' : 'KC Thamrin',
                'bank_address'    => 'Jl. Jendral Sudirman Kav. 22-23, Jakarta',
                'contact_person'  => 'Account Officer (08123456789)',
                'nominal'         => $nominal,
            ]);

            // Buat history penerbitan BG
            BgHistory::create([
                'bank_garansi_id'   => $bg->id,
                'previous_nominal'  => $nominal,
                'new_nominal'       => $nominal,
                'previous_exp_date' => $now->copy()->subYear()->format('Y-m-d'),
                'new_exp_date'      => $todayStr,
                'remarks'           => 'Penerbitan BG Awal - Aktif dan Jatuh Tempo Hari Ini (' . $todayStr . ')',
                'created_by'        => $adminUser->id ?? 1,
                'created_at'        => $now->copy()->subYear(),
            ]);

            $createdBgs[] = [
                'customer'  => $cust->name . ' (' . $cust->code . ')',
                'bg_number' => $bgNumber,
                'nominal'   => 'Rp ' . number_format($nominal, 0, ',', '.'),
                'exp_date'  => $todayStr . ' (Hari ini)'
            ];
        }

        $this->command->info("==================================================================");
        $this->command->info("Data dummy Bank Garansi (BG) yang EXPIRED TODAY berhasil dibuat!");
        $this->command->info("==================================================================");
        foreach ($createdBgs as $i => $item) {
            $num = $i + 1;
            $this->command->info("{$num}. [BG Expired Today] {$item['customer']}");
            $this->command->info("   No. BG: {$item['bg_number']} | Nominal: {$item['nominal']} | Exp Date: {$item['exp_date']}");
        }
        $this->command->info("------------------------------------------------------------------");
        $this->command->info("Langkah Pengujian:");
        $this->command->info("1. Jalankan command scheduler:");
        $this->command->info("   php artisan bg:check-expired");
        $this->command->info("2. Buka menu BG Recommendations di aplikasi web:");
        $this->command->info("   URL: /bg-recommendations (Tab Expiring BG)");
        $this->command->info("   Data BG expired today di atas akan otomatis muncul dengan status 'Pending' dan tombol 'Process'.");
        $this->command->info("==================================================================");
    }
}
