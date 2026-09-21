<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FinanceApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $log;
    public $submission;
    public $approver;

    public function __construct($log, $submission, $approver)
    {
        $this->log = $log;
        $this->submission = $submission;
        $this->approver = $approver;
    }

    public function build()
    {
        $customerName = $this->submission->recommendation->customer->name ?? 'Distributor';
        $formCode = $this->submission->form_code;

        return $this->subject("Approval Required: Verifikasi Sertifikat Bank Garansi - {$customerName} ({$formCode})")
                    ->view('mail.finance_approval');
    }
}