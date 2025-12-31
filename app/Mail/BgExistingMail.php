<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BankGaransi;

class BgExistingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bg;

    public function __construct(BankGaransi $bg)
    {
        $this->bg = $bg;
    }

    public function build()
    {
        return $this->subject('Notification: BG Update (Existing) - ' . $this->bg->customer->name)
                    ->view('mail.bg-existing');
    }
}