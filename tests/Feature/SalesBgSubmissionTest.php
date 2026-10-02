<?php

use App\Models\User;
use App\Models\Customer\Customer;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgSubmission;
use App\Models\BG\BgDetail;
use App\Models\Master\ApprovalLog;
use App\Jobs\ProcessFinanceApprovalEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Storage::fake('public');
    Queue::fake();
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
    DB::table('approval_logs')->truncate();
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
    Role::firstOrCreate(['name' => 'admin-rtm', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
});

function createSalesTestUser(): User
{
    $deptId = DB::table('departments')->insertGetId([
        'name' => 'Sales & Marketing',
        'slug' => 'sales-marketing',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::create([
        'nik' => 'SALES-' . rand(1000, 9999),
        'username' => 'sales.' . rand(1000, 9999),
        'email' => 'sales.' . rand(1000, 9999) . '@example.com',
        'name' => 'Sales Staff',
        'department_id' => $deptId,
        'atasan_nik' => 'HEAD123',
        'password' => bcrypt('password'),
    ]);

    return $user;
}

function createSecretaryTestUser(): User
{
    $deptId = DB::table('departments')->insertGetId([
        'name' => 'Finance Dept',
        'slug' => 'finance-dept',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::create([
        'nik' => 'SEC-' . rand(1000, 9999),
        'username' => 'secretary.' . rand(1000, 9999),
        'email' => 'sec.' . rand(1000, 9999) . '@example.com',
        'name' => 'Bu Rita Secretary',
        'department_id' => $deptId,
        'atasan_nik' => 'FINHEAD',
        'password' => bcrypt('password'),
    ]);

    $user->assignRole('secretary-finance');

    return $user;
}

function createTestCustomer(array $overrides = []): Customer
{
    return Customer::create(array_merge([
        'name'         => 'PT Mitra Usaha',
        'code'         => 'CUST-' . rand(1000, 9999),
        'pic'          => 'Budi Santoso',
        'status'       => 'active',
        'bank_garansi' => 'YA',
    ], $overrides));
}

it('fails sales submission store if unauthenticated', function () {
    $response = $this->postJson('/bg/sales-submissions', []);
    $response->assertStatus(401);
});

it('validates required fields for sales submission', function () {
    $user = createSalesTestUser();
    $this->actingAs($user);

    $response = $this->post('/bg/sales-submissions', []);

    $response->assertSessionHasErrors([
        'customer_id',
        'submission_type',
        'bank_name',
        'bg_number',
        'bg_nominal',
        'exp_date',
        'warkat_file',
    ]);
});

it('validates existing_bg_id is required when submission_type is adendum', function () {
    $user = createSalesTestUser();
    $this->actingAs($user);

    $customer = createTestCustomer([
        'name' => 'PT Mitra Sejahtera',
        'code' => 'CUST001',
    ]);

    $response = $this->post('/bg/sales-submissions', [
        'customer_id'     => $customer->id,
        'submission_type' => 'adendum',
        'bank_name'       => 'Bank Central Asia',
        'bg_number'       => 'BG-TEST-001',
        'bg_nominal'      => '50.000.000',
        'exp_date'        => '2027-12-31',
        'warkat_file'     => UploadedFile::fake()->create('sertifikat_bg.pdf', 500, 'application/pdf'),
    ]);

    $response->assertSessionHasErrors(['existing_bg_id']);
});

it('successfully stores Tambah BG sales submission and dispatches approval to Bu Rita', function () {
    $salesUser = createSalesTestUser();
    $secretaryUser = createSecretaryTestUser();
    $this->actingAs($salesUser);

    $customer = createTestCustomer([
        'name' => 'PT Maju Makmur Bersama',
        'code' => 'CUST002',
    ]);

    $postData = [
        'customer_id'     => $customer->id,
        'submission_type' => 'tambah_bg',
        'bank_name'       => 'Bank Mandiri',
        'branch_name'     => 'KCU Sudirman',
        'bg_number'       => 'BG-TB-MDR-001',
        'bg_nominal'      => '100.000.000',
        'issued_date'     => '2026-10-01',
        'exp_date'        => '2027-10-01',
        'warkat_file'     => UploadedFile::fake()->create('sertifikat_bg.pdf', 1024, 'application/pdf'),
        'signed_document' => UploadedFile::fake()->create('dokumen_adendum.pdf', 512, 'application/pdf'),
        'notes'           => 'Pengajuan Tambah BG untuk ekspansi wilayah',
    ];

    $response = $this->post('/bg/sales-submissions', $postData);

    $response->assertRedirect(route('sales-submissions.index'));
    $response->assertSessionHas('success');

    // Check BgSubmission record
    $this->assertDatabaseHas('bg_submissions', [
        'submission_type' => 'tambah_bg',
        'bg_number'       => 'BG-TB-MDR-001',
        'bg_nominal'      => 100000000.00,
        'status'          => 'waiting_approval',
    ]);

    $submission = BgSubmission::where('bg_number', 'BG-TB-MDR-001')->first();
    expect($submission)->not->toBeNull();
    expect($submission->form_code)->toStartWith('TB-');
    expect($submission->warkat_file_path)->not->toBeNull();
    expect($submission->signed_document_path)->not->toBeNull();

    // Check BankGaransi record in draft status
    $this->assertDatabaseHas('bank_garansi', [
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-TB-MDR-001',
        'bg_type'     => 'new',
        'is_adendum'  => 0,
        'base_bg_id'  => null,
        'bg_nominal'  => 100000000.00,
        'status'      => 'draft',
    ]);

    // Check BgDetail
    $bg = BankGaransi::where('bg_number', 'BG-TB-MDR-001')->first();
    $this->assertDatabaseHas('bg_details', [
        'bank_garansi_id' => $bg->id,
        'bank_name'       => 'Bank Mandiri',
        'branch_name'     => 'KCU Sudirman',
        'nominal'         => 100000000.00,
    ]);

    // Check ApprovalLog generated for secretary-finance (Bu Rita)
    $this->assertDatabaseHas('approval_logs', [
        'category'     => 'BG',
        'sub_category' => 'Lampiran D',
        'related_id'   => $submission->id,
        'approver_nik' => $secretaryUser->nik,
        'status'       => 'Pending',
    ]);

    // Check ProcessFinanceApprovalEmail job dispatched
    Queue::assertPushed(ProcessFinanceApprovalEmail::class);
});

it('successfully stores Adendum BG sales submission linked to existing BG', function () {
    $salesUser = createSalesTestUser();
    $secretaryUser = createSecretaryTestUser();
    $this->actingAs($salesUser);

    $customer = createTestCustomer([
        'name' => 'PT Sumber Rejeki Abadi',
        'code' => 'CUST003',
    ]);

    // Create existing approved BG to be adendumed
    $existingBg = BankGaransi::create([
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-ORIGINAL-999',
        'bg_type'     => 'new',
        'bg_nominal'  => 50000000.00,
        'status'      => 'approved',
        'issued_date' => '2025-10-01',
        'exp_date'    => '2026-10-01',
        'created_by'  => $salesUser->id,
    ]);

    $postData = [
        'customer_id'     => $customer->id,
        'submission_type' => 'adendum',
        'existing_bg_id'  => $existingBg->id,
        'bank_name'       => 'Bank BCA',
        'branch_name'     => 'KCU Thamrin',
        'bg_number'       => 'BG-AD-BCA-888',
        'bg_nominal'      => '75.000.000',
        'issued_date'     => '2026-10-01',
        'exp_date'        => '2027-10-01',
        'warkat_file'     => UploadedFile::fake()->create('sertifikat_bg_adendum.pdf', 1024, 'application/pdf'),
        'notes'           => 'Perpanjangan dan peningkatan nilai jaminan melalui Adendum',
    ];

    $response = $this->post('/bg/sales-submissions', $postData);

    $response->assertRedirect(route('sales-submissions.index'));
    $response->assertSessionHas('success');

    // Check BgSubmission record with prefix AD-
    $this->assertDatabaseHas('bg_submissions', [
        'submission_type' => 'adendum',
        'bg_number'       => 'BG-AD-BCA-888',
        'bg_nominal'      => 75000000.00,
        'status'          => 'waiting_approval',
    ]);

    $submission = BgSubmission::where('bg_number', 'BG-AD-BCA-888')->first();
    expect($submission)->not->toBeNull();
    expect($submission->form_code)->toStartWith('AD-');

    // Check BankGaransi record linked to existing BG
    $this->assertDatabaseHas('bank_garansi', [
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-AD-BCA-888',
        'bg_type'     => 'existing',
        'is_adendum'  => 1,
        'base_bg_id'  => $existingBg->id,
        'bg_nominal'  => 75000000.00,
        'status'      => 'draft',
    ]);

    // Check ApprovalLog generated
    $this->assertDatabaseHas('approval_logs', [
        'category'     => 'BG',
        'sub_category' => 'Lampiran D',
        'related_id'   => $submission->id,
        'approver_nik' => $secretaryUser->nik,
        'status'       => 'Pending',
    ]);
});

it('returns active bank garansi list for a customer via getCustomerBgs endpoint', function () {
    $salesUser = createSalesTestUser();
    $this->actingAs($salesUser);

    $customer = createTestCustomer([
        'name' => 'PT Harapan Bangsa',
        'code' => 'CUST004',
    ]);

    // 1 approved BG and 1 draft BG
    $approvedBg = BankGaransi::create([
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-ACTIVE-001',
        'bg_type'     => 'new',
        'bg_nominal'  => 50000000.00,
        'status'      => 'approved',
        'exp_date'    => '2027-01-01',
        'created_by'  => $salesUser->id,
    ]);
    $approvedBg->details()->create([
        'bank_name' => 'Bank Mandiri',
        'nominal'   => 50000000.00,
    ]);

    $draftBg = BankGaransi::create([
        'customer_id' => $customer->id,
        'bg_number'   => 'BG-DRAFT-002',
        'bg_type'     => 'new',
        'bg_nominal'  => 20000000.00,
        'status'      => 'draft',
        'exp_date'    => '2027-01-01',
        'created_by'  => $salesUser->id,
    ]);

    $response = $this->getJson("/bg/sales-submissions/customer-bgs/{$customer->id}");

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    $bgs = $response->json('bgs');
    expect($bgs)->toHaveCount(1);
    expect($bgs[0]['bg_number'])->toBe('BG-ACTIVE-001');
    expect($bgs[0]['details'])->toHaveCount(1);
    expect($bgs[0]['details'][0]['bank_name'])->toBe('Bank Mandiri');
});
