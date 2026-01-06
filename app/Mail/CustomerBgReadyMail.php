<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\DocumentHelper;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Models\User; // Pastikan Model User di-import

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

        $nomorPkd = $customer->no_pkd;
        $submissionDates = BgSubmission::where('bg_recommendation_id', $rec->id)->pluck('created_at');

        $totalBgDiserahkan = BankGaransi::where('customer_id', $customer->id)
                                ->whereIn('created_at', $submissionDates)
                                ->sum('bg_nominal');

        $financeUser = User::role('head-finance')->first();
        $financeName = $financeUser ? $financeUser->name : 'Finance Dept. Head Tidak Diketahui';

        $salesUser = User::role('head-SNM')->first();
        $salesName = $salesUser ? $salesUser->name : 'S&M Dept. Head Tidak Diketahui';
        $data = [
            'submission' => $this->submission,
            'rec' => $rec,
            'customer' => $customer,
            'nomor_pkd' => $nomorPkd,
            'total_bg_diserahkan' => $totalBgDiserahkan,
            'finance_name' => $financeName,
            'sales_name' => $salesName
        ];

        $pdfLampiranD = Pdf::loadView('pdf.lampiran_d', $data)->output();

        return $this->subject('Dokumen Perhitungan BG (Lampiran D) - ' . $customer->name)
                    ->view('mail.mail-lampiran-d')
                    ->attachData($pdfLampiranD, 'Lampiran_D.pdf', ['mime' => 'application/pdf']);
    }
}
