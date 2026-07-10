<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Master\ApprovalLog;
use App\Models\Customer\Customer;
use App\Models\User;
use App\Jobs\CustomerJob;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ResendPendingApproval extends Command
{
    protected $signature = 'approval:resend-pending';
    protected $description = 'Resend approval emails only to the current active turn (Exactly 1 Working Day later)';

    public function handle()
    {
        if (Carbon::now()->isWeekend()) {
            return;
        }

        $batasWaktu = Carbon::now()->subWeekday(); 
        $pendingCustomerIds = ApprovalLog::where('category', 'Customer')
            ->where('status', 'Pending')
            ->distinct()
            ->pluck('related_id');

        foreach ($pendingCustomerIds as $customerId) {
            
            $activeLog = ApprovalLog::where('category', 'Customer')
                ->where('related_id', $customerId)
                ->where('status', 'Pending')
                ->orderBy('level', 'asc')
                ->first();

            if ($activeLog && $activeLog->updated_at <= $batasWaktu) {
                $customer = Customer::find($customerId);
                $approver = User::where('nik', $activeLog->approver_nik)->first();
                if ($customer && $approver && $approver->email) {
                    $newToken = Str::uuid()->toString();
                    $activeLog->update([
                        'token' => $newToken, 
                        'updated_at' => now()
                    ]);

                    $recipients = [[
                        'nik' => $approver->nik,
                        'email' => $approver->email,
                        'name' => $approver->name,
                        'level' => $activeLog->level,
                        'is_first' => ($activeLog->level == 1),
                        'is_it' => $approver->hasRole('it')
                    ]];

                    CustomerJob::dispatch($customer->id, $recipients, $newToken, 'approval');
                    Log::info("Auto-resend (1 Working Day) to ACTIVE approver: {$approver->email} for Customer ID: {$customer->id}");
                }
            }
        }
    }
}