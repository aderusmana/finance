<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BgSubmission;

class BgUpdateDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $pdfContentBase64;
    public $type;

    public function __construct(BgSubmission $submission, $pdfContentBase64, $type = 'existing')
    {
        $this->submission = $submission;
        $this->pdfContentBase64 = $pdfContentBase64;
        $this->type = $type;
    }

    public function build()
    {
        $pdfDecoded = base64_decode($this->pdfContentBase64);

        $subject = ($this->type == 'existing')
            ? 'Dokumen Update Bank Garansi & Link Upload (Action Required)'
            : 'Dokumen Extension Bank Garansi & Link Upload (Action Required)';

        return $this->subject($subject)
                    ->view('mail.bg_update_upload')
                    ->with([
                        'submission' => $this->submission,
                        'type' => $this->type
                    ])
                    ->attachData($pdfDecoded, 'Formulir_BG_'.$this->type.'.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
