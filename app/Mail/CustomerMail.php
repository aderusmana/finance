<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Customer\Customer;
use App\Models\User;

class CustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $approver;
    public $data;

    public function __construct(Customer $customer, User $approver = null, array $data = [])
    {
        $this->customer = $customer;
        $this->approver = $approver;
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        $subject = $this->data['subject'] ?? ('Approval Request: ' . ($this->customer->name ?? 'Customer'));

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        // Use a simple view. If not present, developer can create `mail.customer-approval` view.
        return new Content(
            view: $this->data['view'] ?? 'mail.customer-approval',
            with: $this->data
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
