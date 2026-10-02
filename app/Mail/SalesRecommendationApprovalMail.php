<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalesRecommendationApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $log;
    public $recommendation;
    public $approver;

    public function __construct($log, $recommendation, $approver)
    {
        $this->log = $log;
        $this->recommendation = $recommendation;
        $this->approver = $approver;
    }

    public function build()
    {
        $custName = $this->recommendation->customer->name ?? 'Distributor';
        return $this->subject("[Approval Required] Rekomendasi Bank Garansi - {$custName}")
                    ->view('mail.sales_recommendation_approval');
    }
}
