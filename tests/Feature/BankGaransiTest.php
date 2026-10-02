<?php

use App\Models\User;
use App\Models\Customer\Customer;
use App\Models\BG\BankGaransi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Queue::fake();
    Mail::fake();

    // Reset database state
    DB::statement('PRAGMA foreign_keys = OFF');
    DB::table('bank_garansi')->truncate();
    DB::table('bg_details')->truncate();
    Customer::truncate();
    User::truncate();
    DB::table('departments')->truncate();
    DB::statement('PRAGMA foreign_keys = ON');
});

function createBgTestUser(): User
{
    $departmentId = DB::table('departments')->insertGetId([
        'name' => 'Finance Dept',
        'slug' => 'finance-dept',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return User::create([
        'nik' => 'FIN-' . rand(1000, 9999),
        'username' => 'finance.' . rand(1000, 9999),
        'email' => 'fin.' . rand(1000, 9999) . '@example.com',
        'name' => 'Finance Staff',
        'department_id' => $departmentId,
        'atasan_nik' => 'BOSS456',
        'password' => bcrypt('password'),
    ]);
}

it('fails bank garansi creation if unauthorized', function () {
    $response = $this->postJson('/bg/bg-list', []);
    $response->assertStatus(401);
});

it('validates required fields for bank garansi', function () {
    $user = createBgTestUser();
    $this->actingAs($user);

    $response = $this->postJson('/bg/bg-list', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['customer_id', 'items']);
});

it('validates bank garansi items elements', function () {
    $user = createBgTestUser();
    $this->actingAs($user);

    $postData = [
        'customer_id' => 1,
        'items' => [
            [
                'bg_number' => '', // empty
                'nominal' => -100,  // invalid
                'bank_name' => '',  // empty
            ]
        ]
    ];

    $response = $this->postJson('/bg/bg-list', $postData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'items.0.bg_number',
            'items.0.nominal',
            'items.0.bank_name',
        ]);
});

it('successfully stores bank garansi drafts and details', function () {
    $user = createBgTestUser();
    $this->actingAs($user);

    // Create a customer
    $customer = Customer::create([
        'name' => 'Client Company A',
        'pic' => 'John Client',
    ]);

    $postData = [
        'customer_id' => $customer->id,
        'items' => [
            [
                'bg_number' => 'BG-XYZ-12345',
                'bg_type' => 'new',
                'nominal' => 25000000.00,
                'bank_name' => 'Bank Mandiri',
                'branch_name' => 'Jakarta Kota',
                'bank_address' => 'Sudirman Kav 21',
                'contact_person' => 'Budi',
                'issued_date' => '2026-07-01',
                'exp_date' => '2027-07-01',
            ]
        ]
    ];

    $response = $this->postJson('/bg/bg-list', $postData);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Bank Garansi created successfully!');

    // Check bank_garansi table
    $this->assertDatabaseHas('bank_garansi', [
        'customer_id' => $customer->id,
        'bg_number' => 'BG-XYZ-12345',
        'bg_type' => 'new',
        'bg_nominal' => 25000000.00,
        'status' => 'draft',
        'created_by' => $user->id,
    ]);

    $bg = BankGaransi::where('bg_number', 'BG-XYZ-12345')->first();

    // Check base_bg_id auto-assigned to self when no previous BG exists
    expect($bg->base_bg_id)->toBe($bg->id);

    // Check bg_details table
    $this->assertDatabaseHas('bg_details', [
        'bank_garansi_id' => $bg->id,
        'bank_name' => 'Bank Mandiri',
        'branch_name' => 'Jakarta Kota',
        'bank_address' => 'Sudirman Kav 21',
        'contact_person' => 'Budi',
        'nominal' => 25000000.00,
    ]);
});
