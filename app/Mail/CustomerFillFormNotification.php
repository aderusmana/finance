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
    public $isUploadAdminNotif;
    public $submission;

    /**
     * Create a new message instance.
     *
     * @param BgRecommendation $recommendation
     */
    public function __construct(BgRecommendation $recommendation, $isUploadAdminNotif = false, $submission = null)
    {
        $this->recommendation = $recommendation;
        $this->isUploadAdminNotif = $isUploadAdminNotif;
        $this->submission = $submission;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->isUploadAdminNotif 
            ? 'Customer Uploaded Bank Guarantee Document' 
            : 'Confirmation & Bank Guarantee Form';

        return $this->subject($subject)
                    ->view('mail.input-bank-upload')
                    ->with([
                        'recommendation' => $this->recommendation,
                        'isUploadAdminNotif' => $this->isUploadAdminNotif,
                        'submission' => $this->submission,
                    ]);
    }
}
