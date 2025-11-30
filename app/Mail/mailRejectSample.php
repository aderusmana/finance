<?php

namespace App\Mail;

use App\Models\Requisition\Requisition;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class MailRejectSample extends Mailable
{
    use Queueable, SerializesModels;

    public $requisition;
    public $rejectingApproverName;
    public $notes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Requisition $requisition, string $rejectingApproverName, string $notes)
    {
        $this->requisition = $requisition;
        $this->rejectingApproverName = $rejectingApproverName;
        $this->notes = $notes;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Informasi: Requisition Anda (' . $this->requisition->no_srs . ') Ditolak',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'page.sample.reject',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
