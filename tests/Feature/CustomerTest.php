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
        'credit_limit' => '10,000,000', // Pre-validation logic will clean this to float
        'bank_garansi' => 'TIDAK',
        'lead_time' => '3',
        'file_npwp' => UploadedFile::fake()->create('npwp.pdf', 300),
        'file_nib' => UploadedFile::fake()->create('nib.pdf', 250),
        'file_ktp' => UploadedFile::fake()->create('ktp.jpg', 150),
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
