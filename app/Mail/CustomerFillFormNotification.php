<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Opsional: gunakan jika ingin antrian (queue)
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BgRecommendation;

class CustomerFillFormNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $recommendation;

    /**
     * Create a new message instance.
     *
     * @param BgRecommendation $recommendation
     */
    public function __construct(BgRecommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Konfirmasi & Pengisian Form Bank Garansi')
                    ->view('mail.input-bank-upload')
                    ->with([
                        'recommendation' => $this->recommendation,
                    ]);
    }
}
