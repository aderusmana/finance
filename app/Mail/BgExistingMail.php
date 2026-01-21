<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgRecommendation;

class BgExistingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bg;
    public $rec;

    public function __construct(BankGaransi $bg, BgRecommendation $rec)
    {
        $this->bg = $bg;
        $this->rec = $rec;
    }

    public function build()
    {
        return $this->subject('Notification: BG Update (Existing) - ' . $this->bg->customer->name)
                    ->view('mail.bg-existing');
    }
}
