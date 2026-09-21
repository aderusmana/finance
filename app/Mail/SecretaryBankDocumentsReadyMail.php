<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BgSubmission;
use App\Models\User;

class SecretaryBankDocumentsReadyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $submission;
    public $secretary;

    public function __construct(BgSubmission $submission, User $secretary)
    {
        $this->submission = $submission;
        $this->secretary  = $secretary;
    }

    public function build()
    {
        $customerName = $this->submission->recommendation->customer->name ?? 'Distributor';
        $formCode     = $this->submission->form_code;

        return $this->subject("Berkas Siap TTD Basah & Pengajuan Bank: {$customerName} ({$formCode})")
                    ->view('mail.secretary_bank_documents_ready');
    }
}
