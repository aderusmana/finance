<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Master\ApprovalLog;
use App\Models\User;
use App\Models\Customer\Customer;
use App\Jobs\CustomerJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendApprovalReminder extends Command
{
    protected $signature = 'approval:send-reminder';
    protected $description = 'Mengirim email reminder harian untuk approval yang tertunda (H+1)';

    public function handle()
    {
        $customers = Customer::whereIn('status_approval', ['Pending', 'Processing'])->get();
        $count = 0;

        foreach ($customers as $customer) {
            // 2. Cari log approval yang AKTIF saat ini (Pending dengan level terendah)
            $activeLog = ApprovalLog::where('category', 'Customer')
                ->where('related_id', $customer->id)
                ->where('status', 'Pending')
                ->orderBy('level', 'asc')
                ->first();

            if (!$activeLog) continue;

            $activationDate = null;

            // 3. Tentukan kapan approval ini "sampai" ke meja approver tersebut
            if ($activeLog->level == 1) {
                // Level 1: Dihitung sejak customer dibuat / di-recall
                $activationDate = $activeLog->created_at;
            } else {
                // Level > 1: Dihitung sejak level sebelumnya selesai (Approved)
                $prevLog = ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $customer->id)
                    ->where('level', $activeLog->level - 1)
                    ->first();

                if ($prevLog && $prevLog->status === 'Approved') {
                    $activationDate = $prevLog->updated_at;
                } else {
                    continue; 
                }
            }

            // 4. Cek apakah waktunya sudah lewat hari ini (Masuk H+1)
            if ($activationDate && $activationDate < Carbon::today()) {
                $approver = User::where('nik', $activeLog->approver_nik)->first();

                if ($approver && $approver->email) {
                    $recipients = [[
                        'nik' => $approver->nik,
                        'email' => $approver->email,
                        'name' => $approver->name,
                        'level' => $activeLog->level,
                        'is_first' => false,
                        'is_it' => $approver->hasRole('it')
                    ]];

                    CustomerJob::dispatch($customer->id, $recipients, $activeLog->token, 'approval');
                    $count++;
                }
            }
        }

        $this->info("Berhasil mengirim $count reminder approval.");
        Log::info("Daily reminder sent to $count active approvers.");
    }
}