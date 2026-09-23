<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BgSubmission;

class CreditLimitUpdatedItMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $submission;
    public $customer;
    public $recommendation;
    public $approvedCreditLimit;
    public $validatorName;

    /**
     * Create a new message instance.
     */
    public function __construct(BgSubmission $submission, $validatorName = 'Secretary Finance', $customCreditLimit = null)
    {
        $this->submission = $submission;
        $this->recommendation = $submission->recommendation;
        $this->customer = $this->recommendation ? $this->recommendation->customer : null;
        $this->approvedCreditLimit = ($customCreditLimit !== null && $customCreditLimit > 0)
            ? (float) $customCreditLimit
            : ($this->recommendation ? (float) $this->recommendation->credit_limit_updated : 0);
        $this->validatorName = $validatorName;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $custName = $this->customer ? $this->customer->name : 'Customer';

        return $this->subject("[INFO - Background Updated] Credit Limit Sync: {$custName}")
                    ->view('mail.credit-limit-updated-it');
    }
}
