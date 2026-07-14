<?php

use App\Jobs\CustomerJob;
use App\Models\Customer\Customer;
use App\Models\Master\ApprovalLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    // Clear logs to ensure clean state
    ApprovalLog::truncate();
    Customer::truncate();
    User::truncate();
    DB::table('departments')->truncate();
});

it('resends pending approval emails on weekdays when logs are older than 1 working day', function () {
    Queue::fake();

    // 1. Setup test data
    $department = DB::table('departments')->insertGetId([
        'name' => 'IT Department',
        'slug' => 'it-department',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::create([
        'nik' => 'EMP123',
        'username' => 'john.doe',
        'email' => 'john.doe@example.com',
        'name' => 'John Doe',
        'department_id' => $department,
        'atasan_nik' => 'BOSS456',
        'password' => bcrypt('password'),
    ]);

    $customer = Customer::create([
        'name' => 'Test Company',
        'pic' => 'Test PIC',
    ]);

    // Create log updated 2 days ago (older than 1 working day)
    $twoDaysAgo = Carbon::now()->subDays(2);
    $log = ApprovalLog::create([
        'category' => 'Customer',
        'related_id' => $customer->id,
        'approver_nik' => $user->nik,
        'status' => 'Pending',
        'level' => 1,
        'token' => 'old-token-value',
    ]);

    // Explicitly set updated_at in DB
    DB::table('approval_logs')->where('id', $log->id)->update([
        'updated_at' => $twoDaysAgo,
    ]);

    // 2. Mock time to a weekday (e.g. Wednesday, July 15, 2026)
    Carbon::setTestNow(Carbon::create(2026, 7, 15, 10, 0, 0));

    // 3. Run the command
    $this->artisan('approval:resend-pending')
        ->assertExitCode(0);

    // 4. Assertions
    $refreshedLog = ApprovalLog::find($log->id);

    // Token must have changed
    expect($refreshedLog->token)->not->toBe('old-token-value');

    // updated_at must be reset to now
    expect($refreshedLog->updated_at->toDateTimeString())->toBe(Carbon::now()->toDateTimeString());

    // Job must be dispatched
    Queue::assertPushed(CustomerJob::class, function ($job) use ($customer, $user) {
        return $job->customerId === $customer->id && $job->recipients[0]['nik'] === $user->nik;
    });

    Carbon::setTestNow(); // Clean up
});

it('skips resending pending approval emails on weekends', function () {
    Queue::fake();

    // 1. Setup test data
    $department = DB::table('departments')->insertGetId([
        'name' => 'IT Department',
        'slug' => 'it-department',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $user = User::create([
        'nik' => 'EMP123',
        'username' => 'john.doe',
        'email' => 'john.doe@example.com',
        'name' => 'John Doe',
        'department_id' => $department,
        'atasan_nik' => 'BOSS456',
        'password' => bcrypt('password'),
    ]);

    $customer = Customer::create([
        'name' => 'Test Company',
        'pic' => 'Test PIC',
    ]);

    // Create log updated 2 days ago
    $twoDaysAgo = Carbon::now()->subDays(2);
    $log = ApprovalLog::create([
        'category' => 'Customer',
        'related_id' => $customer->id,
        'approver_nik' => $user->nik,
        'status' => 'Pending',
        'level' => 1,
        'token' => 'old-token-value',
    ]);

    DB::table('approval_logs')->where('id', $log->id)->update([
        'updated_at' => $twoDaysAgo,
    ]);

    // 2. Mock time to a Saturday (e.g. Saturday, July 18, 2026)
    Carbon::setTestNow(Carbon::create(2026, 7, 18, 10, 0, 0));

    // 3. Run the command
    $this->artisan('approval:resend-pending')
        ->assertExitCode(0);

    // 4. Assertions
    $refreshedLog = ApprovalLog::find($log->id);

    // Token must NOT change because it's weekend and command should early return
    expect($refreshedLog->token)->toBe('old-token-value');

    // updated_at must NOT change
    expect($refreshedLog->updated_at->toDateTimeString())->toBe($twoDaysAgo->toDateTimeString());

    // Job must NOT be dispatched
    Queue::assertNotPushed(CustomerJob::class);

    Carbon::setTestNow(); // Clean up
});
