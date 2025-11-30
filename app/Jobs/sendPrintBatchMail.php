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
use Illuminate\Support\Facades\Mail;
use App\Mail\printBatchMail;
use App\Models\Requisition\Tracking;
use Illuminate\Support\Facades\Log;

class sendPrintBatchMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $approver;
    protected $requisition;
    protected $tracking;

    /**
     * Create a new job instance.
     */
    public function __construct(User $approver, Requisition $requisition, Tracking $tracking)
    {
        $this->approver = $approver;
        $this->requisition = $requisition;
        $this->tracking = $tracking;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Load requisition dengan relasi yang diperlukan
        $requisitionWithData = Requisition::with(['customer', 'requester', 'requisitionItems.itemMaster'])
            ->find($this->requisition->id);

        $quickOkLink = route('complain.warehouse.approval', [
            'id' => $this->tracking->requisition_id,
            'token' => $this->tracking->token,
        ]);

        $okWithReviewLink = route('complain.warehouse.review', [
            'id' => $this->tracking->requisition_id,
            'token' => $this->tracking->token,
        ]);

        try {
            Mail::to($this->approver->email)->send(new printBatchMail(
                $this->approver,
                $requisitionWithData ?? $this->requisition,
                $this->tracking,
                $quickOkLink,
                $okWithReviewLink
            ));

        } catch (\Exception $e) {
            Log::error('Job - sendPrintBatchMail: Failed to send email', [
                'error' => $e->getMessage(),
                'to_email' => $this->approver->email,
                'requisition_id' => $this->requisition->id,
                'token' => $this->tracking->token
            ]);
            throw $e;
        }
    }
}
