<?php

namespace App\Mail;

use App\Models\Requisition\Requisition;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class MailRejectFreeGoods extends Mailable
{
    use Queueable, SerializesModels;

    public $requisition;
    public $rejectingApproverName;
    public $notes;

    public function __construct(Requisition $requisition, string $rejectingApproverName, ?string $notes)
    {
        $this->requisition = $requisition;
        $this->rejectingApproverName = $rejectingApproverName;
        $this->notes = $notes;
    }

    public function envelope()
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Requisition Rejected: ' . $this->requisition->no_srs,
        );
    }

    public function content()
    {
        return new Content(
            // Re-use view reject dari freegoods
            view: 'page.freegoods.reject', 
        );
    }

    public function attachments()
    {
        return [];
    }
}