<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\BG\BgSubmission;

class SalesFillBgNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;
    public $salesUser;

    public function __construct(BgSubmission $submission, $salesUser = null)
    {
        $this->submission = $submission;
        $this->salesUser = $salesUser;
    }

    public function build()
    {
        $customer = $this->submission->recommendation->customer ?? null;
        $custName = $customer ? $customer->name : 'Distributor';

        return $this->subject("[Tindakan Diperlukan] Lengkapi Data Bank Garansi - {$custName}")
                    ->view('mail.sales_fill_bg_notification')
                    ->with([
                        'submission' => $this->submission,
                        'customer'   => $customer,
                        'salesUser'  => $this->salesUser,
                    ]);
    }
}
