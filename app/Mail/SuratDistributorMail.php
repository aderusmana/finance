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

        return $this->subject('BG Expiration Notice - ' . $this->customer->name)
                    ->view('mail.bank-distributor-mail') // Pastikan view ini ada
                    ->with([
                        'title' => 'Distributor Notice Letter',
                        'content' => 'Please find attached the Notice Letter regarding the expiration of the Distributor Bank Guarantee.',
                        'link' => $this->downloadLink
                    ])
                    ->attachData($pdf, 'Surat_Pemberitahuan_Distributor.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}