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
        $this->command->info("Memulai pembuatan data dummy alur Bank Garansi (BG) dari tabel CUSTOMERS...");

        // Hapus data customer placeholder CUST-BG-00X jika sebelumnya sempat dibuat
        $placeholderCustomerIds = Customer::where('code', 'LIKE', 'CUST-BG-%')->pluck('id');
        if ($placeholderCustomerIds->isNotEmpty()) {
            BankGaransi::whereIn('customer_id', $placeholderCustomerIds)->delete();
            BgRecommendation::whereIn('customer_id', $placeholderCustomerIds)->delete();
            Customer::whereIn('id', $placeholderCustomerIds)->delete();
        }

        // Update semua customer yang memiliki email '-' menjadi email valid agar queue mail tidak error
        $allInvalidCust = Customer::where('email', '-')->orWhereNull('email')->orWhere('email', '')->get();
        foreach ($allInvalidCust as $c) {
            $c->update(['email' => 'distributor.' . Str::slug($c->name) . '@example.com']);
        }

        $getValidEmail = function($cust) {
            if (!empty($cust->email) && filter_var($cust->email, FILTER_VALIDATE_EMAIL)) {
                return $cust->email;
            }
            return 'distributor.' . Str::slug($cust->name) . '@example.com';
        };

        // 1. Pastikan Konfigurasi Tax & Limit Rules Tersedia
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

        // 2. Pastikan Role & Approval Path Tersedia
        if (!Role::where('name', 'manager-finance')->exists()) {
            Role::create(['name' => 'manager-finance', 'guard_name' => 'web']);
        }
        ApprovalPath::updateOrCreate(
            ['category' => 'BG', 'sub_category' => 'Lampiran D'],
            ['sequence_approvers' => ['manager-finance'], 'updated_at' => now()]
        );

        // Ambil user admin & finance untuk referensi relasi
        $adminUser = User::role('super-admin')->first() ?? User::first();
        $financeUser = User::role('manager-finance')->first() ?? User::role('head-finance')->first() ?? $adminUser;

        // Ambil data customer asli dari tabel customers
        $customers = Customer::orderBy('id', 'asc')->take(6)->get();

        if ($customers->count() < 6) {
            $this->command->warn("Jumlah customer di tabel customers kurang dari 6. Menggunakan {$customers->count()} customer yang ada.");
        }

        $now = Carbon::now();

        // =========================================================================
        // SCENARIO 1: Customer Asli #1 -> Siap Rekomendasi (Tahap 1: Pending Rekomendasi)
        // =========================================================================
        $cust1 = $customers->get(0);
        if ($cust1) {
            $cust1->update([
                'bank_garansi'            => 'YA',
                'term_of_payment'         => $cust1->term_of_payment ?: 30,
                'lead_time'               => $cust1->lead_time ?: 7,
                'join_date'               => $cust1->join_date ?: $now->copy()->subYears(3)->format('Y-m-d'),
                'area'                    => $cust1->area && $cust1->area !== '-' ? $cust1->area : 'Jawa Barat',
                'city'                    => $cust1->city ?: 'Bandung',
                'email'                   => $getValidEmail($cust1),
                'status'                  => 'active',
                'status_approval'         => 'approved',
                'approved_credit_limit'   => 1500000000,
            ]);

            // Bersihkan data BG/Rekomendasi lama untuk customer ini agar fresh
            BankGaransi::where('customer_id', $cust1->id)->delete();
            $rec1 = BgRecommendation::updateOrCreate(
                ['customer_id' => $cust1->id],
                [
                    'tax_id'                   => $tax->id,
                    'average'                  => 300000000,
                    'top'                      => (float)$cust1->term_of_payment,
                    'lead_time'                => (float)$cust1->lead_time,
                    'inflation'                => 130,
                    'current_bg'               => 150000000,
                    'recommended_credit_limit' => 0,
                    'rounded_credit_limit'     => 0,
                    'set_bg'                   => 0,
                    'status'                   => 'pending',
                    'token'                    => null,
                    'notes'                    => 'Siap Diproses Admin: Periode ' . $now->format('F Y'),
                    'created_at'               => $now,
                    'updated_at'               => $now,
                ]
            );

            BgPeriod::where('bg_recommendation_id', $rec1->id)->delete();
            BgPeriod::insert([
                ['bg_recommendation_id' => $rec1->id, 'period_date' => $now->copy()->subMonths(3)->startOfMonth()->format('Y-m-d'), 'amount' => 95000000, 'created_at' => $now, 'updated_at' => $now],
                ['bg_recommendation_id' => $rec1->id, 'period_date' => $now->copy()->subMonths(2)->startOfMonth()->format('Y-m-d'), 'amount' => 100000000, 'created_at' => $now, 'updated_at' => $now],
                ['bg_recommendation_id' => $rec1->id, 'period_date' => $now->copy()->subMonths(1)->startOfMonth()->format('Y-m-d'), 'amount' => 105000000, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        // =========================================================================
        // SCENARIO 2: Customer Asli #2 -> Siap Isi Portal Mandiri (Tahap 2: Input Form Bank)
        // =========================================================================
        $cust2 = $customers->get(1);
        $tokenInputForm = 'token-input-' . Str::random(20);
        if ($cust2) {
            $cust2->update([
                'bank_garansi'            => 'YA',
                'term_of_payment'         => $cust2->term_of_payment ?: 45,
                'lead_time'               => $cust2->lead_time ?: 14,
                'join_date'               => $cust2->join_date ?: $now->copy()->subYears(4)->format('Y-m-d'),
                'area'                    => $cust2->area && $cust2->area !== '-' ? $cust2->area : 'Jawa Timur',
                'city'                    => $cust2->city ?: 'Surabaya',
                'email'                   => $getValidEmail($cust2),
                'status'                  => 'active',
                'status_approval'         => 'approved',
            ]);

            BankGaransi::where('customer_id', $cust2->id)->delete();
            $rec2 = BgRecommendation::updateOrCreate(
                ['customer_id' => $cust2->id],
                [
                    'tax_id'                   => $tax->id,
                    'average'                  => 450000000,
                    'top'                      => (float)$cust2->term_of_payment,
                    'lead_time'                => (float)$cust2->lead_time,
                    'inflation'                => 130,
                    'current_bg'               => 200000000,
                    'recommended_credit_limit' => 643500000,
                    'rounded_credit_limit'     => 644000000,
                    'set_bg'                   => 250000000,
                    'credit_limit_updated'     => 1666666667,
                    'status'                   => 'process',
                    'token'                    => $tokenInputForm,
                    'notes'                    => json_encode(['action' => 'new']),
                    'updated_at'               => $now,
                ]
            );
        }

        // =========================================================================
        // SCENARIO 3: Customer Asli #3 -> Siap Upload Scan TTD (Tahap 3: Form Upload)
        // =========================================================================
        $cust3 = $customers->get(2);
        $tokenUploadForm = 'token-upload-' . Str::random(20);
        if ($cust3) {
            $cust3->update([
                'bank_garansi'            => 'YA',
                'term_of_payment'         => $cust3->term_of_payment ?: 30,
                'lead_time'               => $cust3->lead_time ?: 5,
                'join_date'               => $cust3->join_date ?: $now->copy()->subYears(6)->format('Y-m-d'),
                'area'                    => $cust3->area && $cust3->area !== '-' ? $cust3->area : 'DKI Jakarta',
                'city'                    => $cust3->city ?: 'Jakarta Utara',
                'email'                   => $getValidEmail($cust3),
                'status'                  => 'active',
                'status_approval'         => 'approved',
            ]);

            BankGaransi::where('customer_id', $cust3->id)->delete();
            $rec3 = BgRecommendation::updateOrCreate(
                ['customer_id' => $cust3->id],
                [
                    'tax_id'                   => $tax->id,
                    'average'                  => 600000000,
                    'top'                      => (float)$cust3->term_of_payment,
                    'lead_time'                => (float)$cust3->lead_time,
                    'inflation'                => 130,
                    'current_bg'               => 250000000,
                    'set_bg'                   => 300000000,
                    'credit_limit_updated'     => 1500000000,
                    'status'                   => 'waiting_upload',
                    'notes'                    => json_encode(['action' => 'new']),
                    'token'                    => null,
                    'updated_at'               => $now,
                ]
            );

            $bg3 = BankGaransi::create([
                'customer_id' => $cust3->id,
                'bg_number'   => 'BG-' . $now->year . '-0103',
                'bg_type'     => 'new',
                'bg_nominal'  => 300000000,
                'status'      => 'draft',
                'created_by'  => $adminUser->id ?? 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $bg3->update(['base_bg_id' => $bg3->id]);
            BgDetail::create([
                'bank_garansi_id' => $bg3->id,
                'bank_name'       => 'Bank Central Asia (BCA)',
                'branch_name'     => 'KCU Sudirman',
                'bank_address'    => 'Jl. Jendral Sudirman Kav. 22-23, Jakarta',
                'contact_person'  => 'Rina Kartika (08119876543)',
                'nominal'         => 300000000,
            ]);

            BgSubmission::where('bg_recommendation_id', $rec3->id)->delete();
            $sub3 = BgSubmission::create([
                'bg_recommendation_id' => $rec3->id,
                'form_code'            => 'NEW-' . $now->format('Ymd') . '-0103',
                'status'               => 'awaiting_upload',
                'token'                => $tokenUploadForm,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        // =========================================================================
        // SCENARIO 4: Customer Asli #4 -> Dokumen Diunggah & Siap Direview Admin (Tahap 4)
        // =========================================================================
        $cust4 = $customers->get(3);
        if ($cust4) {
            $cust4->update([
                'bank_garansi'            => 'YA',
                'term_of_payment'         => $cust4->term_of_payment ?: 60,
                'lead_time'               => $cust4->lead_time ?: 10,
                'join_date'               => $cust4->join_date ?: $now->copy()->subYears(5)->format('Y-m-d'),
                'area'                    => $cust4->area && $cust4->area !== '-' ? $cust4->area : 'Jawa Tengah',
                'city'                    => $cust4->city ?: 'Semarang',
                'email'                   => $getValidEmail($cust4),
                'status'                  => 'active',
                'status_approval'         => 'approved',
            ]);

            BankGaransi::where('customer_id', $cust4->id)->delete();
            $rec4 = BgRecommendation::updateOrCreate(
                ['customer_id' => $cust4->id],
                [
                    'tax_id'                   => $tax->id,
                    'average'                  => 500000000,
                    'top'                      => (float)$cust4->term_of_payment,
                    'lead_time'                => (float)$cust4->lead_time,
                    'inflation'                => 130,
                    'current_bg'               => 200000000,
                    'set_bg'                   => 250000000,
                    'credit_limit_updated'     => 1666666667,
                    'status'                   => 'waiting_upload',
                    'notes'                    => json_encode(['action' => 'new']),
                    'token'                    => null,
                    'updated_at'               => $now,
                ]
            );

            $bg4 = BankGaransi::create([
                'customer_id' => $cust4->id,
                'bg_number'   => 'BG-' . $now->year . '-0104',
                'bg_type'     => 'new',
                'bg_nominal'  => 250000000,
                'status'      => 'draft',
                'created_by'  => $adminUser->id ?? 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $bg4->update(['base_bg_id' => $bg4->id]);
            BgDetail::create([
                'bank_garansi_id' => $bg4->id,
                'bank_name'       => 'Bank Mandiri',
                'branch_name'     => 'KC Semarang Pemuda',
                'bank_address'    => 'Jl. Pemuda No. 70, Semarang',
                'contact_person'  => 'Bambang Hartono (081234567890)',
                'nominal'         => 250000000,
            ]);

            BgSubmission::where('bg_recommendation_id', $rec4->id)->delete();
            $sub4 = BgSubmission::create([
                'bg_recommendation_id' => $rec4->id,
                'form_code'            => 'NEW-' . $now->format('Ymd') . '-0104',
                'signed_document_path' => 'storage/bg_documents/sample_signed.pdf',
                'submitted_at'         => $now,
                'upload_completed_at'  => $now,
                'status'               => 'uploaded', // SIAP DI-REVIEW ADMIN DI /bg-submissions
                'token'                => null,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        // =========================================================================
        // SCENARIO 5: Customer Asli #5 -> Menunggu Persetujuan Finance (Tahap 5)
        // =========================================================================
        $cust5 = $customers->get(4);
        $tokenApprovalEmail = 'token-approval-' . Str::random(20);
        if ($cust5) {
            $cust5->update([
                'bank_garansi'            => 'YA',
                'term_of_payment'         => $cust5->term_of_payment ?: 30,
                'lead_time'               => $cust5->lead_time ?: 7,
                'join_date'               => $cust5->join_date ?: $now->copy()->subYears(7)->format('Y-m-d'),
                'area'                    => $cust5->area && $cust5->area !== '-' ? $cust5->area : 'Sumatera Utara',
                'city'                    => $cust5->city ?: 'Medan',
                'email'                   => $getValidEmail($cust5),
                'status'                  => 'active',
                'status_approval'         => 'approved',
            ]);

            BankGaransi::where('customer_id', $cust5->id)->delete();
            $rec5 = BgRecommendation::updateOrCreate(
                ['customer_id' => $cust5->id],
                [
                    'tax_id'                   => $tax->id,
                    'average'                  => 750000000,
                    'top'                      => (float)$cust5->term_of_payment,
                    'lead_time'                => (float)$cust5->lead_time,
                    'inflation'                => 130,
                    'current_bg'               => 400000000,
                    'set_bg'                   => 500000000,
                    'credit_limit_updated'     => 2500000000,
                    'status'                   => 'waiting_upload',
                    'notes'                    => json_encode(['action' => 'new']),
                    'token'                    => null,
                    'updated_at'               => $now,
                ]
            );

            $bg5 = BankGaransi::create([
                'customer_id' => $cust5->id,
                'bg_number'   => 'BG-' . $now->year . '-0105',
                'bg_type'     => 'new',
                'bg_nominal'  => 500000000,
                'status'      => 'draft',
                'created_by'  => $adminUser->id ?? 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $bg5->update(['base_bg_id' => $bg5->id]);
            $bgDetail5 = BgDetail::create([
                'bank_garansi_id' => $bg5->id,
                'bank_name'       => 'Bank Negara Indonesia (BNI)',
                'branch_name'     => 'KCU Medan',
                'bank_address'    => 'Jl. Pemuda No. 12, Medan',
                'contact_person'  => 'Dewi Anggraini (081377889900)',
                'nominal'         => 500000000,
            ]);

            BgSubmission::where('bg_recommendation_id', $rec5->id)->delete();
            $sub5 = BgSubmission::create([
                'bg_recommendation_id' => $rec5->id,
                'form_code'            => 'NEW-' . $now->format('Ymd') . '-0105',
                'signed_document_path' => 'storage/bg_documents/sample_signed.pdf',
                'submitted_at'         => $now,
                'upload_completed_at'  => $now,
                'status'               => 'waiting_approval', // SIAP DI-APPROVE DI /approvals/inbox
                'token'                => null,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);

            // Dokumen Lampiran D & Versi 1
            $lampiranD5 = LampiranD::updateOrCreate(
                ['bg_submission_id' => $sub5->id],
                ['version_latest' => 1, 'created_by' => $adminUser->id ?? 1]
            );

            $snapshot5 = [
                'nama_distributor'    => $cust5->name,
                'kota'                => $cust5->city,
                'wilayah_kerja'       => $cust5->area,
                'periode'             => $now->translatedFormat('F Y'),
                'rata_rata_penjualan' => $rec5->average,
                'syarat_pembayaran'   => $rec5->top,
                'lead_time'           => $rec5->lead_time,
                'faktor_fluktuasi'    => $rec5->inflation,
                'limit_kredit'        => $rec5->credit_limit_updated,
                'nilai_bg_ditetapkan' => $rec5->set_bg,
                'nilai_bg_diserahkan' => $bg5->bg_nominal,
                'details'             => [
                    $bgDetail5->id => [
                        'bank_name'   => 'Bank Negara Indonesia (BNI)',
                        'branch_name' => 'KCU Medan',
                        'nominal'     => 500000000
                    ]
                ]
            ];

            $ver5 = LampiranDVersion::create([
                'lampiran_d_id' => $lampiranD5->id,
                'version_no'    => 1,
                'data_snapshot' => $snapshot5,
                'file_path'     => $sub5->signed_document_path,
                'generated_by'  => $adminUser->id ?? 1,
                'generated_at'  => $now,
                'remarks'       => 'Initial Version - Forwarded to Finance Approval'
            ]);
            $lampiranD5->update(['active_version_id' => $ver5->id]);

            // Approval Log
            ApprovalLog::where('category', 'BG')->where('related_id', $sub5->id)->delete();
            ApprovalLog::create([
                'category'     => 'BG',
                'sub_category' => 'Lampiran D',
                'related_id'   => $sub5->id,
                'approver_nik' => $financeUser->nik ?? ($adminUser->nik ?? 'ADMIN01'),
                'status'       => 'Pending',
                'level'        => 1,
                'token'        => $tokenApprovalEmail,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        // =========================================================================
        // SCENARIO 6: Customer Asli #6 -> BG Aktif Mendekati Jatuh Tempo (Expiring Soon)
        // =========================================================================
        $cust6 = $customers->get(5);
        if ($cust6) {
            $cust6->update([
                'bank_garansi'            => 'YA',
                'term_of_payment'         => $cust6->term_of_payment ?: 30,
                'lead_time'               => $cust6->lead_time ?: 7,
                'join_date'               => $cust6->join_date ?: $now->copy()->subYears(4)->format('Y-m-d'),
                'area'                    => $cust6->area && $cust6->area !== '-' ? $cust6->area : 'Bali & Nusa Tenggara',
                'city'                    => $cust6->city ?: 'Denpasar',
                'email'                   => $getValidEmail($cust6),
                'status'                  => 'active',
                'status_approval'         => 'approved',
            ]);

            BankGaransi::where('customer_id', $cust6->id)->delete();
            $bg6 = BankGaransi::create([
                'customer_id' => $cust6->id,
                'bg_number'   => 'BG-' . ($now->year - 1) . '-0106',
                'bg_type'     => 'new',
                'bg_nominal'  => 350000000,
                'issued_date' => $now->copy()->subMonths(11)->format('Y-m-d'),
                'exp_date'    => $now->copy()->addDays(20)->format('Y-m-d'), // Jatuh tempo 20 hari lagi (EXPIRING SOON)
                'status'      => 'approved',
                'created_by'  => $adminUser->id ?? 1,
                'created_at'  => $now->copy()->subMonths(11),
                'updated_at'  => $now,
            ]);
            $bg6->update(['base_bg_id' => $bg6->id]);
            BgDetail::create([
                'bank_garansi_id' => $bg6->id,
                'bank_name'       => 'Bank Rakyat Indonesia (BRI)',
                'branch_name'     => 'KCU Denpasar Gajah Mada',
                'bank_address'    => 'Jl. Gajah Mada No. 1, Denpasar',
                'contact_person'  => 'Ketut Rai (08123890123)',
                'nominal'         => 350000000,
            ]);

            BgHistory::create([
                'bank_garansi_id'   => $bg6->id,
                'previous_nominal'  => 300000000,
                'new_nominal'       => 350000000,
                'previous_exp_date' => $now->copy()->subMonths(11)->format('Y-m-d'),
                'new_exp_date'      => $now->copy()->addDays(20)->format('Y-m-d'),
                'remarks'           => 'Penerbitan Bank Garansi Awal Disetujui',
                'created_by'        => $adminUser->id ?? 1,
                'created_at'        => $now->copy()->subMonths(11)
            ]);
        }

        $this->command->info("Selesai! Data dummy alur Bank Garansi berhasil di-generate dari tabel customers.");
        $this->command->info("Daftar Customer yang Diperbarui:");
        if ($cust1) $this->command->info("1. [Pending Rekomendasi] {$cust1->name} ({$cust1->code}) -> Menu: BG Recommendations");
        if ($cust2) $this->command->info("2. [Portal Input Bank]   {$cust2->name} ({$cust2->code}) -> URL: /form/{$tokenInputForm}");
        if ($cust3) $this->command->info("3. [Portal Upload TTD]   {$cust3->name} ({$cust3->code}) -> URL: /upload/{$tokenUploadForm}");
        if ($cust4) $this->command->info("4. [Siap Review Admin]   {$cust4->name} ({$cust4->code}) -> Menu: BG Submissions");
        if ($cust5) $this->command->info("5. [Inbox Approval]      {$cust5->name} ({$cust5->code}) -> Menu: BG Approvals Inbox / Token: {$tokenApprovalEmail}");
        if ($cust6) $this->command->info("6. [BG Expiring Soon]    {$cust6->name} ({$cust6->code}) -> Menu: BG List - Expiring Soon & Reports");
    }
}
