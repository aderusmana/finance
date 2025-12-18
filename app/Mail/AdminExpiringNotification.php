<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminExpiringNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bgs; // Data list BG yang expired

    public function __construct($bgs)
    {
        $this->bgs = $bgs;
    }

    public function build()
    {
        return $this->subject('Peringatan: Bank Garansi Expiring (H-60)')
                    ->view('mail.admin-expiring-list');
    }
}
