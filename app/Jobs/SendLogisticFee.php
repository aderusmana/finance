<?php
namespace App\Jobs;

use App\Mail\LogisticFeeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLogisticFee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $log;
    protected $logisticData;

    public function __construct($log, $logisticData)
    {
        $this->log = $log;
        $this->logisticData = $logisticData;
    }

    public function handle()
    {
        $approver = User::where('nik', $this->log->approver_nik)->first();

        if (!$approver) {
            Log::warning('SendLogisticFee: approver user not found', [
                'approver_nik' => $this->log->approver_nik,
                'approval_log_id' => $this->log->id ?? null,
            ]);
            return;
        }

        if (empty($approver->email)) {
            Log::warning('SendLogisticFee: approver email is empty', [
                'approver_nik' => $approver->nik,
                'approver_name' => $approver->name,
                'approval_log_id' => $this->log->id ?? null,
            ]);
            return;
        }

        try {
            Mail::to($approver->email)->send(new LogisticFeeMail(
                'request',
                $this->logisticData,
                [
                    'log' => $this->log,
                    'approverName' => $approver->name
                ]
            ));
        } catch (\Throwable $e) {
            Log::error('SendLogisticFee: failed sending email', [
                'approver_nik' => $approver->nik,
                'approver_email' => $approver->email,
                'approval_log_id' => $this->log->id ?? null,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
