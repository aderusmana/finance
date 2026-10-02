<?php

use App\Models\User;
use App\Models\Customer\Customer;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgSubmission;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BgDetail;
use App\Models\BG\LampiranD;
use App\Models\Master\ApprovalLog;
use App\Models\Master\ApprovalPath;
use App\Mail\SecretaryBankDocumentsReadyMail;
use App\Jobs\ProcessFinanceApprovalEmail;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Storage::fake('public');
    Queue::fake();
    Mail::fake();
    Notification::fake();

    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    } else {
        DB::statement('PRAGMA foreign_keys = OFF;');
    }

    DB::table('bg_submissions')->truncate();
    DB::table('bank_garansi')->truncate();
    DB::table('bg_details')->truncate();
    DB::table('bg_recommendations')->truncate();
    DB::table('lampiran_d')->truncate();
    DB::table('lampiran_d_versions')->truncate();
    DB::table('approval_logs')->truncate();
    DB::table('approval_paths')->truncate();
    DB::table('customers')->truncate();
    DB::table('users')->truncate();
    DB::table('departments')->truncate();
    DB::table('roles')->truncate();
    DB::table('model_has_roles')->truncate();

    if (DB::getDriverName() === 'mysql') {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    } else {
        DB::statement('PRAGMA foreign_keys = ON;');
    }

    Role::firstOrCreate(['name' => 'secretary-finance', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'manager-finance', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'head-finance', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin-rtm', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'it', 'guard_name' => 'web']);

    ApprovalPath::firstOrCreate(
        ['category' => 'BG', 'sub_category' => 'Lampiran D'],
        [
            'name'               => 'Path Approval Lampiran D',
            'sequence_approvers' => json_encode(['secretary-finance']),
        ]
    );
});

function createRtmTestUser(): User
{
    $deptId = DB::table('departments')->insertGetId([
        'name' => 'RTM Dept',
        'slug' => 'rtm-dept',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::create([
        'nik' => 'RTM-' . rand(1000, 9999),
        'username' => 'rtm.' . rand(1000, 9999),
        'email' => 'rtm.' . rand(1000, 9999) . '@smi.co.id',
        'name' => 'Admin RTM Staff',
        'department_id' => $deptId,
        'atasan_nik' => 'HEAD-RTM',
        'password' => bcrypt('password'),
    ]);

    $user->assignRole('admin-rtm');
    return $user;
}

function createRitaUser(): User
{
    $deptId = DB::table('departments')->insertGetId([
        'name' => 'Finance Dept',
        'slug' => 'finance-dept',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::create([
        'nik' => 'RITA-001',
        'username' => 'rita.finance',
        'email' => 'rita.rahayu@smi.co.id',
        'name' => 'Bu Rita Rahayu',
        'department_id' => $deptId,
        'atasan_nik' => 'FINHEAD',
        'password' => bcrypt('password'),
    ]);

    $user->assignRole('secretary-finance');
    return $user;
}

function createTestCustomerForApproval(array $overrides = []): Customer
{
    return Customer::create(array_merge([
        'name'         => 'PT Sumber Rezeki',
        'code'         => 'CUST-' . rand(1000, 9999),
        'pic'          => 'Hendra Wijaya',
        'status'       => 'active',
        'bank_garansi' => 'YA',
    ], $overrides));
}

it('dispatches web notification and email to Bu Rita when Admin RTM verifies upload', function () {
    $adminRtm = createRtmTestUser();
    $buRita = createRitaUser();
    $customer = createTestCustomerForApproval();

    $rec = BgRecommendation::create([
        'customer_id'           => $customer->id,
        'status'                => 'pending_distributor',
        'top'                   => 30,
        'lead_time'             => 14,
        'average'               => 500000000,
        'credit_limit_updated'  => 800000000,
        'set_bg'                => 300000000,
    ]);

    $bg = BankGaransi::create([
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-DRAFT-000',
        'bg_nominal'  => 300000000,
        'status'      => 'draft',
        'created_at'  => now(),
    ]);

    $detail = BgDetail::create([
        'bank_garansi_id' => $bg->id,
        'bank_name'       => 'Bank Mandiri',
        'branch_name'     => 'Cabang Jakarta Pusat',
        'nominal'         => 300000000,
        'created_at'      => now(),
    ]);

    $submission = BgSubmission::create([
        'bg_recommendation_id' => $rec->id,
        'form_code'            => 'BG-TEST-001',
        'status'               => 'uploaded',
        'bg_nominal'           => 300000000,
        'signed_document_path' => 'bg_documents/test_signed.pdf',
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);

    $response = $this->actingAs($adminRtm)->postJson(route('bg-submissions.process-review', $submission->id), [
        'action_type' => 'verify_upload'
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $submission->refresh();
    expect($submission->status)->toBe('waiting_bank_issuance');

    // Pastikan web notification (lonceng) terkirim ke Bu Rita
    Notification::assertSentTo($buRita, SystemNotification::class);

    // Pastikan email SecretaryBankDocumentsReadyMail diantrekan ke Bu Rita
    Mail::assertQueued(SecretaryBankDocumentsReadyMail::class, function ($mail) use ($buRita, $submission) {
        return $mail->hasTo($buRita->email) && $mail->submission->id === $submission->id;
    });
});

it('dispatches web notification and approval email to Bu Rita when Admin RTM inputs certificate data', function () {
    $adminRtm = createRtmTestUser();
    $buRita = createRitaUser();
    $customer = createTestCustomerForApproval();

    $rec = BgRecommendation::create([
        'customer_id'           => $customer->id,
        'status'                => 'pending_distributor',
        'top'                   => 30,
        'lead_time'             => 14,
        'average'               => 500000000,
        'credit_limit_updated'  => 800000000,
        'set_bg'                => 300000000,
    ]);

    $bg = BankGaransi::create([
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-DRAFT-001',
        'bg_nominal'  => 300000000,
        'status'      => 'draft',
        'created_at'  => now(),
    ]);

    $detail = BgDetail::create([
        'bank_garansi_id' => $bg->id,
        'bank_name'       => 'Bank Mandiri',
        'branch_name'     => 'Cabang Jakarta Pusat',
        'created_at'      => now(),
    ]);

    $submission = BgSubmission::create([
        'bg_recommendation_id' => $rec->id,
        'form_code'            => 'BG-TEST-002',
        'status'               => 'waiting_bank_issuance',
        'bg_nominal'           => 300000000,
        'signed_document_path' => 'bg_documents/test_signed.pdf',
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);

    $payload = [
        'action_type'         => 'edit_submit',
        'bg_number'           => 'BG-MANDIRI-998877',
        'exp_date'            => now()->addYear()->format('Y-m-d'),
        'nama_distributor'    => $customer->name,
        'kota'                => 'Jakarta',
        'wilayah_kerja'       => 'Jabodetabek',
        'periode'             => '2026',
        'rata_rata_penjualan' => 500000000,
        'syarat_pembayaran'   => 30,
        'lead_time'           => 14,
        'faktor_fluktuasi'    => 130,
        'limit_kredit'        => 800000000,
        'nilai_bg_ditetapkan' => 300000000,
        'nilai_bg_diserahkan' => 300000000,
        'details'             => [
            $detail->id => [
                'nominal'   => 300000000,
                'bg_number' => 'BG-MANDIRI-998877',
                'exp_date'  => now()->addYear()->format('Y-m-d')
            ]
        ],
        'warkat_file'         => UploadedFile::fake()->create('sertifikat_bg_asli.pdf', 500, 'application/pdf'),
    ];

    $response = $this->actingAs($adminRtm)->post(route('bg-submissions.process-review', $submission->id), $payload);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $submission->refresh();
    expect($submission->status)->toBe('waiting_approval');
    expect($submission->bg_number)->toBe('BG-MANDIRI-998877');

    // Pastikan web notification (lonceng) terkirim ke Bu Rita
    Notification::assertSentTo($buRita, SystemNotification::class);

    // Pastikan ApprovalLog terbuat untuk Bu Rita
    $log = ApprovalLog::where('related_id', $submission->id)
        ->where('category', 'BG')
        ->where('approver_nik', $buRita->nik)
        ->first();

    expect($log)->not->toBeNull();
    expect($log->token)->not->toBeNull();
    expect($log->status)->toBe('Pending');

    // Pastikan ProcessFinanceApprovalEmail di-dispatch
    Queue::assertPushed(ProcessFinanceApprovalEmail::class);
});

it('allows Bu Rita to approve submission and activate BG via web approval inbox', function () {
    $buRita = createRitaUser();
    $customer = createTestCustomerForApproval();

    $rec = BgRecommendation::create([
        'customer_id'           => $customer->id,
        'status'                => 'pending_distributor',
        'top'                   => 30,
        'lead_time'             => 14,
        'average'               => 500000000,
        'credit_limit_updated'  => 800000000,
        'set_bg'                => 300000000,
    ]);

    $bg = BankGaransi::create([
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-DRAFT-002',
        'bg_nominal'  => 300000000,
        'status'      => 'draft',
        'created_at'  => now(),
    ]);

    $submission = BgSubmission::create([
        'bg_recommendation_id' => $rec->id,
        'form_code'            => 'BG-TEST-003',
        'status'               => 'waiting_approval',
        'bg_nominal'           => 300000000,
        'bg_number'            => 'BG-BCA-554433',
        'exp_date'             => now()->addYear()->format('Y-m-d'),
        'warkat_file_path'     => 'bg_documents/warkat/test_scan.pdf',
        'signed_document_path' => 'bg_documents/test_signed.pdf',
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);

    $log = ApprovalLog::create([
        'category'      => 'BG',
        'sub_category'  => 'Lampiran D',
        'related_id'    => $submission->id,
        'approver_nik'  => $buRita->nik,
        'approver_name' => $buRita->name,
        'status'        => 'Pending',
        'level'         => 1,
        'token'         => Str::random(60),
    ]);

    // Bu Rita approve dari web dashboard /bg/approvals/process
    $response = $this->actingAs($buRita)->postJson(route('bg-approvals.process'), [
        'id'     => $submission->id,
        'action' => 'approve',
        'notes'  => 'Disetujui oleh Bu Rita melalui dashboard web'
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $submission->refresh();
    $bg->refresh();
    $customer->refresh();
    $log->refresh();

    // Verifikasi closing status
    expect($submission->status)->toBe('completed');
    expect($bg->status)->toBe('approved');
    expect($bg->bg_number)->toBe('BG-BCA-554433');
    expect($customer->credit_limit)->toEqual(800000000);
    expect($log->status)->toBe('Approved');
});

it('allows Bu Rita to one-click approve submission and activate BG via email token', function () {
    $buRita = createRitaUser();
    $customer = createTestCustomerForApproval();

    $rec = BgRecommendation::create([
        'customer_id'           => $customer->id,
        'status'                => 'pending_distributor',
        'top'                   => 30,
        'lead_time'             => 14,
        'average'               => 500000000,
        'credit_limit_updated'  => 900000000,
        'set_bg'                => 400000000,
    ]);

    $bg = BankGaransi::create([
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-DRAFT-003',
        'bg_nominal'  => 400000000,
        'status'      => 'draft',
        'created_at'  => now(),
    ]);

    $submission = BgSubmission::create([
        'bg_recommendation_id' => $rec->id,
        'form_code'            => 'BG-TEST-004',
        'status'               => 'waiting_approval',
        'bg_nominal'           => 400000000,
        'bg_number'            => 'BG-BNI-112233',
        'exp_date'             => now()->addYear()->format('Y-m-d'),
        'warkat_file_path'     => 'bg_documents/warkat/test_scan.pdf',
        'signed_document_path' => 'bg_documents/test_signed.pdf',
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);

    $token = Str::random(60);
    $log = ApprovalLog::create([
        'category'      => 'BG',
        'sub_category'  => 'Lampiran D',
        'related_id'    => $submission->id,
        'approver_nik'  => $buRita->nik,
        'approver_name' => $buRita->name,
        'status'        => 'Pending',
        'level'         => 1,
        'token'         => $token,
    ]);

    // Bu Rita klik tombol "Quick Approve" dari email
    $response = $this->get(route('approval.process', ['token' => $token, 'action' => 'approve']));

    $response->assertStatus(200);
    $response->assertViewIs('page.customer_portal.form-success');

    $submission->refresh();
    $bg->refresh();
    $customer->refresh();
    $log->refresh();

    // Verifikasi closing status via email 1-click
    expect($submission->status)->toBe('completed');
    expect($bg->status)->toBe('approved');
    expect($bg->bg_number)->toBe('BG-BNI-112233');
    expect($customer->credit_limit)->toEqual(900000000);
    expect($log->status)->toBe('Approved');
    expect($log->token)->toBeNull();
});

it('allows Bu Rita to reject submission with notes via email review form', function () {
    $buRita = createRitaUser();
    $customer = createTestCustomerForApproval();

    $rec = BgRecommendation::create([
        'customer_id'           => $customer->id,
        'status'                => 'pending_distributor',
        'top'                   => 30,
        'lead_time'             => 14,
        'average'               => 500000000,
        'credit_limit_updated'  => 750000000,
        'set_bg'                => 250000000,
    ]);

    $submission = BgSubmission::create([
        'bg_recommendation_id' => $rec->id,
        'form_code'            => 'BG-TEST-005',
        'status'               => 'waiting_approval',
        'bg_nominal'           => 250000000,
        'bg_number'            => 'BG-BRI-001122',
        'exp_date'             => now()->addYear()->format('Y-m-d'),
        'warkat_file_path'     => 'bg_documents/warkat/test_scan.pdf',
        'signed_document_path' => 'bg_documents/test_signed.pdf',
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);

    $token = Str::random(60);
    $log = ApprovalLog::create([
        'category'      => 'BG',
        'sub_category'  => 'Lampiran D',
        'related_id'    => $submission->id,
        'approver_nik'  => $buRita->nik,
        'approver_name' => $buRita->name,
        'status'        => 'Pending',
        'level'         => 1,
        'token'         => $token,
    ]);

    // Bu Rita submit reject dengan alasan revisi
    $response = $this->post(route('approval.submit', ['token' => $token]), [
        'action' => 'reject',
        'notes'  => 'Nomor Bank Garansi pada scan sertifikat tidak sesuai dengan inputan Admin RTM, mohon koreksi kembali.'
    ]);

    $response->assertStatus(200);

    $submission->refresh();
    $log->refresh();

    expect($submission->status)->toBe('rejected_by_finance');
    expect($log->status)->toBe('Rejected');
    expect($log->notes)->toContain('Nomor Bank Garansi');
});
