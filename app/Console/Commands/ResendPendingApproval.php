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
    protected $description = 'Resend approval emails only to the current active turn (lowest pending level) more than 1 day';

    public function handle()
    {
        // Ambil waktu batas (kemarin). Ganti jadi Carbon::now() kalau mau ditest instan
        $yesterday = Carbon::now(); 

        // 1. Ambil semua ID Customer yang sedang memiliki log berstatus 'Pending'
        $pendingCustomerIds = ApprovalLog::where('category', 'Customer')
            ->where('status', 'Pending')
            ->distinct()
            ->pluck('related_id');

        foreach ($pendingCustomerIds as $customerId) {
            
            // 2. Cari pemegang antrean saat ini (Level TERENDAH yang masih Pending)
            $activeLog = ApprovalLog::where('category', 'Customer')
                ->where('related_id', $customerId)
                ->where('status', 'Pending')
                ->orderBy('level', 'asc') // Urutkan dari level paling kecil
                ->first();

            // 3. Pastikan antrean yang aktif ini umurnya sudah lebih dari 1 hari
            if ($activeLog && $activeLog->updated_at <= $yesterday) {
                
                $customer = Customer::find($customerId);
                $approver = User::where('nik', $activeLog->approver_nik)->first();

                if ($customer && $approver && $approver->email) {
                    
                    // Generate token baru agar email/link yang kemarin menjadi invalid
                    $newToken = Str::uuid()->toString();
                    
                    // Update token & updated_at (Reset timer agar tidak dikirim berulang kali hari ini)
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
                        'is_it' => $approver->hasRole('it') // Pengecekan aman jika dia adalah IT
                    ]];

                    CustomerJob::dispatch($customer->id, $recipients, $newToken, 'approval');
                    Log::info("Auto-resend approval email to ACTIVE approver (Level {$activeLog->level}): {$approver->email} for Customer ID: {$customer->id}");
                }
            }
        }
        
        $this->info('Pending approvals processed successfully.');
    }
}