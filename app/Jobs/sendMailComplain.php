<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\ApprovalLog;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\complainMail;

class sendMailComplain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $approver;
    protected $requisition;
    protected $approvalLog;

    /**
     * Create a new job instance.
     */
    public function __construct(User $approver, Requisition $requisition, ApprovalLog $approvalLog)
    {
        $this->approver = $approver;
        $this->requisition = $requisition;
        $this->approvalLog = $approvalLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load requisition dengan relasi payments untuk attachment
        $requisitionWithPayments = Requisition::with(['payments', 'customer', 'requester'])
            ->find($this->requisition->id);

        $approveLink = route('approval.process.direct', [
            'id' => $this->approvalLog->requisition_id,
            'token' => $this->approvalLog->token,
            'status' => 'approve',
        ]);

        $approveWithReviewLink = route('complain.approval.review', [
            'id' => $this->approvalLog->requisition_id,
            'token' => $this->approvalLog->token,
        ]);

        $rejectLink = route('approval.process.direct', [
            'id' => $this->approvalLog->requisition_id,
            'token' => $this->approvalLog->token,
            'status' => 'reject',
        ]);

        Mail::to($this->approver->email)->send(new complainMail(
            $this->approver,
            $requisitionWithPayments ?? $this->requisition,
            $this->approvalLog,
            $approveLink,
            $approveWithReviewLink,
            $rejectLink
        ));
    }
}
