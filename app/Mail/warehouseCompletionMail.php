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

class warehouseCompletionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $requisition;
    public $completedBy;
    public $completionDate;
    public $requester;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Requisition $requisition, 
        User $completedBy, 
        $completionDate = null,
        User $requester
    ) {
        $this->requisition = $requisition;
        $this->completedBy = $completedBy;
        $this->completionDate = $completionDate ?? now();
        $this->requester = $requester;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Warehouse Approval Completed - Requisition ' . $this->requisition->no_srs,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.warehouse-completion',
            with: [
                'requisition' => $this->requisition,
                'completedBy' => $this->completedBy,
                'completionDate' => $this->completionDate,
                'requester' => $this->requester,
                'formattedCompletionDate' => Carbon::parse($this->completionDate)
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