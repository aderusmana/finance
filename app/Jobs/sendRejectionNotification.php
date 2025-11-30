<?php

namespace App\Jobs;

use App\Mail\rejectionNotificationMail;
use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendRejectionNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $requisition;
    public $rejectedBy;
    public $rejectionReason;
    public $rejectionType; // 'approval' or 'warehouse'
    public $rejectionDate;

    /**
     * Create a new job instance.
     */
    public function __construct(
        Requisition $requisition, 
        User $rejectedBy, 
        $rejectionReason = null, 
        $rejectionType = 'approval',
        $rejectionDate = null
    ) {
        $this->requisition = $requisition;
        $this->rejectedBy = $rejectedBy;
        $this->rejectionReason = $rejectionReason;
        $this->rejectionType = $rejectionType;
        $this->rejectionDate = $rejectionDate ?? now();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get requester (person who submitted the requisition)
            $requester = User::where('nik', $this->requisition->requester_nik)->first();
            
            if (!$requester || !$requester->email) {
                Log::warning('Requester not found or has no email for rejection notification', [
                    'requisition_id' => $this->requisition->id,
                    'requester_nik' => $this->requisition->requester_nik
                ]);
                return;
            }

            // Send rejection notification email to requester
            Mail::to($requester->email)->send(new rejectionNotificationMail(
                $this->requisition,
                $this->rejectedBy,
                $this->rejectionReason,
                $this->rejectionType,
                $this->rejectionDate,
                $requester
            ));

            Log::info('Rejection notification email sent successfully', [
                'requisition_id' => $this->requisition->id,
                'rejected_by' => $this->rejectedBy->name,
                'rejection_type' => $this->rejectionType,
                'requester_email' => $requester->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send rejection notification email: ' . $e->getMessage(), [
                'requisition_id' => $this->requisition->id,
                'rejected_by' => $this->rejectedBy->name ?? 'Unknown',
                'rejection_type' => $this->rejectionType,
                'error' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Rejection notification job failed', [
            'requisition_id' => $this->requisition->id,
            'rejected_by' => $this->rejectedBy->name ?? 'Unknown',
            'rejection_type' => $this->rejectionType,
            'error' => $exception->getMessage()
        ]);
    }
}