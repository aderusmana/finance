<?php

namespace App\Jobs;

use App\Mail\warehouseCompletionMail;
use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendWarehouseCompletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $requisition;
    public $completedBy;
    public $completionDate;

    /**
     * Create a new job instance.
     */
    public function __construct(
        Requisition $requisition, 
        User $completedBy, 
        $completionDate = null
    ) {
        $this->requisition = $requisition;
        $this->completedBy = $completedBy;
        $this->completionDate = $completionDate ?? now();
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
                Log::warning('Requester not found or has no email for warehouse completion notification', [
                    'requisition_id' => $this->requisition->id,
                    'requester_nik' => $this->requisition->requester_nik
                ]);
                return;
            }

            // Send warehouse completion notification email to requester
            Mail::to($requester->email)->send(new warehouseCompletionMail(
                $this->requisition,
                $this->completedBy,
                $this->completionDate,
                $requester
            ));

            Log::info('Warehouse completion notification email sent successfully', [
                'requisition_id' => $this->requisition->id,
                'completed_by' => $this->completedBy->name ?? 'System',
                'requester_email' => $requester->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send warehouse completion notification email: ' . $e->getMessage(), [
                'requisition_id' => $this->requisition->id,
                'completed_by' => $this->completedBy->name ?? 'Unknown',
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
        Log::error('Warehouse completion notification job failed', [
            'requisition_id' => $this->requisition->id,
            'completed_by' => $this->completedBy->name ?? 'Unknown',
            'error' => $exception->getMessage()
        ]);
    }
}