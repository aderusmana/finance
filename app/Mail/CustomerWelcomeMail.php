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
        $this->customer = $customer;
        $this->salesRep = $customer->user;
        $this->managerFinance = User::role('manager-finance')->first();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // Subject sesuai dokumen [cite: 3]
            subject: 'Welcome to SMII!',
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
