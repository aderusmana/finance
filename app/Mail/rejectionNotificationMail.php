<?php

namespace App\Mail;

use App\Models\Requisition\Requisition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class rejectionNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $requisition;
    public $rejectedBy;
    public $rejectionReason;
    public $rejectionType;
    public $rejectionDate;
    public $requester;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Requisition $requisition, 
        User $rejectedBy, 
        $rejectionReason = null, 
        $rejectionType = 'approval',
        $rejectionDate = null,
        User $requester
    ) {
        $this->requisition = $requisition;
        $this->rejectedBy = $rejectedBy;
        $this->rejectionReason = $rejectionReason;
        $this->rejectionType = $rejectionType;
        $this->rejectionDate = $rejectionDate ?? now();
        $this->requester = $requester;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->rejectionType === 'warehouse' 
            ? 'Warehouse Approval Rejected - Requisition ' . $this->requisition->no_srs
            : 'Manager Approval Rejected - Requisition ' . $this->requisition->no_srs;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.rejection-notification',
            with: [
                'requisition' => $this->requisition,
                'rejectedBy' => $this->rejectedBy,
                'rejectionReason' => $this->rejectionReason,
                'rejectionType' => $this->rejectionType,
                'rejectionDate' => $this->rejectionDate,
                'requester' => $this->requester,
                'formattedRejectionDate' => Carbon::parse($this->rejectionDate)
                    ->setTimezone('Asia/Jakarta')
                    ->format('d M Y, H:i:s') . ' WIB'
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}