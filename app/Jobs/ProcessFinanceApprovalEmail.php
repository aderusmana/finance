<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Master\ApprovalLog;
use App\Models\BG\BgSubmission;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\FinanceApprovalMail;

class ProcessFinanceApprovalEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $log;
    protected $submission;

    public function __construct(ApprovalLog $log, BgSubmission $submission)
    {
        $this->log = $log;
        $this->submission = $submission;
    }

    public function handle()
    {
        $approver = User::where('nik', $this->log->approver_nik)->first();

        if ($approver && $approver->email) {
            Mail::to($approver->email)->send(new FinanceApprovalMail($this->log, $this->submission, $approver));
        }
    }
}