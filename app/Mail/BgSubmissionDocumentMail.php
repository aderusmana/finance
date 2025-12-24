<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BgSubmission;

class BgSubmissionDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $pdfContentBase64; // Ganti nama variabel biar jelas

    // Terima string Base64 di constructor
    public function __construct(BgSubmission $submission, $pdfContentBase64)
    {
        $this->submission = $submission;
        $this->pdfContentBase64 = $pdfContentBase64;
    }

    public function build()
    {
        // Decode kembali Base64 menjadi Binary PDF
        $pdfDecoded = base64_decode($this->pdfContentBase64);

        return $this->subject('Dokumen Bank Garansi & Link Upload (Action Required)')
                    ->view('mail.input-bank-upload')
                    ->with([
                        'submission' => $this->submission,
                    ])
                    ->attachData($pdfDecoded, 'Formulir_Pengajuan_BG.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
