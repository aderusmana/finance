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
        $type = $this->data['mail_type'] ?? 'approval';

        $prefix = 'Info';
        if ($type === 'approval') {
            $prefix = 'Approval Required';
        } elseif ($type === 'completed') {
            $prefix = 'Approved & Completed';
        } elseif ($type === 'rejected') {
            $prefix = 'Request Rejected';
        }

        return new Envelope(
            subject: "[{$prefix}] Customer Request: " . ($this->customer->name ?? 'Unknown'),
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
                'is_it' => $this->data['is_it'] ?? false,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
