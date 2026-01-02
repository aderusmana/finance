<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\DocumentHelper;
use App\Models\BG\BankGaransi;

class CustomerBgReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;

    public function __construct($submission)
    {
        $this->submission = $submission;
    }

    public function build()
    {
        $rec = $this->submission->recommendation;
        $customer = $rec->customer;

        $bg = BankGaransi::where('customer_id', $customer->id)->latest()->first();

        $nomorPkd = DocumentHelper::generatePKDNumber($rec->id, $customer->name, now());

        $data = [
            'submission' => $this->submission,
            'rec' => $rec,
            'customer' => $customer,
            'bg' => $bg,
            'nomor_pkd' => $nomorPkd,
        ];

        $pdfLampiranD = Pdf::loadView('pdf.lampiran_d', $data)->output();

        return $this->subject('Dokumen Perhitungan BG (Lampiran D) - ' . $customer->name)
                    ->view('mail.mail-lampiran-d')
                    ->attachData($pdfLampiranD, 'Lampiran_D.pdf', ['mime' => 'application/pdf']);
    }
}
