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

class CustomerWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $salesRep;
    public $managerFinance;

    public function __construct(Customer $customer)
    {
        $customer->load(['createdBy', 'user']);

        $this->customer = $customer;
        $this->salesRep = $customer->createdBy ?? $customer->user;
        $this->managerFinance = User::role('manager-finance')->first();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Sinar Meadow ' . $this->customer->name . '!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.welcome-customer',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}