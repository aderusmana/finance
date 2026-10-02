<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratBankMail extends Mailable
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
        $pdf = Pdf::loadView('pdf.surat_bank', $this->dataPdf)->output();

        return $this->subject('Bank Cover Letter - ' . $this->customer->name)
                    ->view('mail.bank-distributor-mail')
                    ->with([
                        'title' => 'Bank Cover Letter',
                        'content' => 'Please find attached the Bank Cover Letter for Bank Guarantee extension purposes.',
                        'link' => $this->downloadLink
                    ])
                    ->attachData($pdf, 'Surat_Pengantar_Bank.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}