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
        // Subjek email yang lebih informatif
        $status = $this->data['mail_type'] == 'approval' ? 'Approval Required' : 'Notification';
        $subject = "[{$status}] New Customer: " . ($this->customer->name ?? 'Unknown');

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.customer-mail',
            with: [
                'approver_name' => $this->data['approver_name'] ?? 'User',
                'token' => $this->data['token'] ?? null,
                'mail_type' => $this->data['mail_type'] ?? 'approval',
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
