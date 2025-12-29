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
        return $this->subject('Approval Required: Bank Garansi - Lampiran D')
                    ->view('mail.finance_approval');
    }
}