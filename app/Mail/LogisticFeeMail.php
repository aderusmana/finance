<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LogisticFeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $type;
    public $logisticData;
    public $extraData;

    public function __construct($type, $logisticData, $extraData = [])
    {
        $this->type = $type; // 'request', 'completed', atau 'rejected'
        $this->logisticData = $logisticData;
        $this->extraData = $extraData; // Array untuk tampung data spesifik (notes, log, dll)
    }

    public function build()
    {
        // Tentukan Subject Email secara dinamis
        $subject = '';
        if ($this->type === 'request') {
            $subject = 'Approval Request: Perubahan Logistic Fee';
        } elseif ($this->type === 'completed') {
            $subject = '[COMPLETED] Perubahan Logistic Fee Telah Disetujui';
        } elseif ($this->type === 'rejected') {
            $subject = '[REJECTED] Pengajuan Logistic Fee Ditolak';
        }

        return $this->subject($subject)
                    ->view('mail.logistic_fee-mail');
    }
}