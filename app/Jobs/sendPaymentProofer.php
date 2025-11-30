<?php

namespace App\Jobs;

use App\Mail\paymentProoferMail;
use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendPaymentProofer implements ShouldQueue
{
    use Queueable;
    protected Requisition $requisition;

    /**
     * Create a new job instance.
     */
    public function __construct(Requisition $requisition)
    {
        $this->requisition = $requisition;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $requester = User::where('nik', $this->requisition->requester_nik)->first();
            
            if (!$requester) {
                Log::error("Requester not found for NIK: {$this->requisition->requester_nik}");
                return;
            }

            // Kirim email peringatan payment proof required
            Log::info("Sending payment proof required email to: {$requester->email} for requisition: {$this->requisition->id}");
            Mail::to($requester->email)->send(new paymentProoferMail($this->requisition));
            
        } catch (\Exception $e) {
            Log::error("Failed to send payment proofer email: " . $e->getMessage());
            throw $e;
        }
    }
}
