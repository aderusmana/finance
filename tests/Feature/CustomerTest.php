<?php

use App\Models\User;
use App\Models\Customer\Customer;
use App\Models\Master\ApprovalLog;
use App\Models\Master\ApprovalPath;
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

    // Reset database state
    ApprovalLog::truncate();
    Customer::truncate();
    User::truncate();
    DB::table('departments')->truncate();
    DB::table('account_groups')->truncate();
    DB::table('customer_classes')->truncate();
    DB::table('approval_paths')->truncate();
    DB::table('roles')->truncate();
    DB::table('model_has_roles')->truncate();

    // Create default super-admin role required by CustomerController
    Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
});

function createCustomerTestUser(string $role = null): User
{
    $departmentId = DB::table('departments')->where('slug', 'sales-dept')->value('id');

    if (!$departmentId) {
        $departmentId = DB::table('departments')->insertGetId([
            'name' => 'Sales Dept',
            'slug' => 'sales-dept',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $user = User::create([
        'nik' => 'NIK-' . rand(100000, 999999),
        'username' => 'username.' . rand(100000, 999999),
        'email' => 'user.' . rand(100000, 999999) . '@example.com',
        'name' => 'Test User',
        'department_id' => $departmentId,
        'atasan_nik' => 'BOSS456',
        'password' => bcrypt('password'),
    ]);

    if ($role) {
        $user->assignRole($role);
    }

    return $user;
}

it('fails customer creation if unauthorized', function () {
    $response = $this->postJson('/customers', []);
    $response->assertStatus(401);
});

it('validates required fields for customer creation', function () {
    $user = createCustomerTestUser();
    $this->actingAs($user);

    $response = $this->postJson('/customers', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'user_id',
            'account_group',
            'customer_class',
            'name',
            'address1',
            'file_npwp',
            'file_nib',
            'file_ktp',
            'city',
            'postal_code',
            'country',
            'email',
            'area',
            'shipping_to_name',
            'shipping_to_address',
            'purchasing_manager_name',
            'purchasing_manager_email',
            'purchasing_manager_telepon',
            'finance_manager_name',
            'finance_manager_email',
            'finance_manager_telepon',
            'penagihan_nama_kontak',
            'penagihan_telepon',
            'penagihan_address',
            'surat_menyurat_address',
            'npwp',
            'ccar',
        ]);
});

it('successfully stores a customer and generates approval flow logs', function () {
    $user = createCustomerTestUser();
    $this->actingAs($user);

    // Create a super-admin for system notifications
    $admin = createCustomerTestUser('super-admin');

    // Create master data dependencies
    $accountGroup = DB::table('account_groups')->insertGetId([
        'name_account_group' => 'Regular Account',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $customerClass = DB::table('customer_classes')->insertGetId([
        'name_class' => 'Class A',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create approval path for general customer registration
    ApprovalPath::create([
        'category' => 'Customer',
        'sub_category' => null,
        'sequence_approvers' => json_encode(['123456']),
    ]);

    // Create boss user in database to link the NIK
    $boss = User::create([
        'nik' => '123456',
        'username' => 'boss.user',
        'email' => 'boss@example.com',
        'name' => 'Boss Approver',
        'department_id' => $user->department_id,
        'atasan_nik' => '789101',
        'password' => bcrypt('password'),
    ]);

    $postData = [
        'user_id' => $user->id,
        'account_group' => $accountGroup,
        'customer_class' => $customerClass,
        'name' => 'Noodles & Co',
        'address1' => 'Gading Serpong Street 15',
        'pic' => 'John Doe',
        'tanggal_npwp' => '2026-01-01',
        'tanggal_nppkp' => '2026-01-01',
        'term_of_payment' => '30',
        'output_tax' => 'PPN',
        'credit_limit' => '10,000,000',
        'bank_garansi' => 'TIDAK',
        'lead_time' => '3',
        'file_npwp' => UploadedFile::fake()->create('npwp.pdf', 300),
        'file_nib' => UploadedFile::fake()->create('nib.pdf', 250),
        'file_ktp' => UploadedFile::fake()->create('ktp.jpg', 150),
        'city' => 'Tangerang',
        'postal_code' => '15810',
        'country' => 'Indonesia',
        'email' => 'general@noodlesco.com',
        'area' => 'Banten',
        'shipping_to_name' => 'Noodles Delivery',
        'shipping_to_address' => 'Gading Serpong Street 15, Tangerang',
        'purchasing_manager_name' => 'Purchasing Mgr',
        'purchasing_manager_email' => 'pur@noodlesco.com',
        'purchasing_manager_telepon' => '08123456789',
        'finance_manager_name' => 'Finance Mgr',
        'finance_manager_email' => 'fin@noodlesco.com',
        'finance_manager_telepon' => '08123456780',
        'penagihan_nama_kontak' => 'Billing CP',
        'penagihan_telepon' => '08123456781',
        'penagihan_address' => 'Billing Address Street 10',
        'surat_menyurat_address' => 'billing@noodlesco.com',
        'npwp' => '01.234.567.8-901.000',
        'ccar' => 'smd_idr',
    ];

    $response = $this->postJson('/customers', $postData);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['success', 'message', 'data']);

    // Check database
    $this->assertDatabaseHas('customers', [
        'name' => 'Noodles & Co',
        'credit_limit' => 0.00,
    ]);

    $customer = Customer::where('name', 'Noodles & Co')->first();

    // Check customer files
    $this->assertDatabaseHas('customer_files', [
        'customer_id' => $customer->id,
    ]);

    // Check approval log
    $this->assertDatabaseHas('approval_logs', [
        'category' => 'Customer',
        'related_id' => $customer->id,
        'approver_nik' => '123456',
        'status' => 'Pending',
    ]);

    // Check queue job
    Queue::assertPushed(\App\Jobs\CustomerJob::class);
});
