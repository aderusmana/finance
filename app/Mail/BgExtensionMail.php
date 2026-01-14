<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BgRecommendation; // Pastikan import ini ada

class BgExtensionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rec;

    /**
     * Create a new message instance.
     *
     * @param BgRecommendation $rec
     */
    public function __construct(BgRecommendation $rec)
    {
        $this->rec = $rec;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $customerName = $this->rec->customer ? $this->rec->customer->name : 'Customer';

        return $this->subject('Action Required: Pengajuan Bank Garansi Tambahan (Extension) - ' . $customerName)
                    ->view('mail.bg-extension');
    }
}
