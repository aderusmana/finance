<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class complainMail extends Mailable
{
    use Queueable, SerializesModels;

    public $approver;
    public $requisition;
    public $approvalLog;
    public $approveLink;
    public $approveWithReviewLink;
    public $rejectLink;

    /**
     * Create a new message instance.
     */
    public function __construct(User $approver, Requisition $requisition, ApprovalLog $approvalLog, $approveLink, $approveWithReviewLink, $rejectLink)
    {
        $this->approver = $approver;
        $this->requisition = $requisition;
        $this->approvalLog = $approvalLog;
        $this->approveLink = $approveLink;
        $this->approveWithReviewLink = $approveWithReviewLink;
        $this->rejectLink = $rejectLink;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Persetujuan Requisition Complain: ' . $this->requisition->no_srs,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.complain-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        try {
            // Query payments berdasarkan requisition_id
            $payment = Payment::where('requisition_id', $this->requisition->id)->first();
            
            Log::info('Payment query for requisition', [
                'requisition_id' => $this->requisition->id,
                'payment_count' => $payment ? 1 : 0
            ]);
            
            // Jika ada payments, tambahkan sebagai attachment
            if ($payment) {
                if ($payment->document_url) {
                    $filePath = storage_path('app/public/' . $payment->document_url);

                    // Pastikan file exists sebelum menambahkan attachment
                    if (file_exists($filePath)) {
                        $fileName = 'payment_proof_' . $this->requisition->id . '_' . basename($payment->document_url);

                        $attachments[] = Attachment::fromPath($filePath)
                            ->as($fileName)
                            ->withMime('application/octet-stream');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing payment attachments', [
                'requisition_id' => $this->requisition->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return $attachments;
    }
}
