<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF

class SuratDistributorMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $customer;
    public $dataPdf;
    public $downloadLink;

    public function __construct($customer, $dataPdf, $downloadLink) 
    { 
        $this->customer = $customer;
        $this->dataPdf = $dataPdf;
        $this->downloadLink = $downloadLink;
    }

    public function build() {
        $pdf = Pdf::loadView('pdf.surat_distributor', $this->dataPdf)->output();

        return $this->subject('Pemberitahuan Jatuh Tempo BG - ' . $this->customer->name)
                    ->view('mail.bank-distributor-mail') // Pastikan view ini ada
                    ->with([
                        'title' => 'Surat Pemberitahuan Distributor',
                        'content' => 'Berikut kami lampirkan Surat Pemberitahuan perihal jatuh tempo Bank Garansi Distributor.',
                        'link' => $this->downloadLink
                    ])
                    ->attachData($pdf, 'Surat_Pemberitahuan_Distributor.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}