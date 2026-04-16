<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class LogisticOrderDistributorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $urlDetail;
    public $urlDownload;

    public function __construct($order)
    {
        $this->order = $order;
        // Generate Signed URL agar aman
        $this->urlDetail = URL::signedRoute('public.lo.detail', ['id' => $order->id]);
        $this->urlDownload = URL::signedRoute('public.lo.download', ['id' => $order->id, 'fromEmail' => true]);
    }

    public function build()
    {
        $formattedLo = 'LO-' . str_pad($this->order->logistic_order_no, 4, '0', STR_PAD_LEFT);

        return $this->subject('Pemberitahuan Logistic Order Baru: ' . $formattedLo)
                    ->view('mail.distributor_order');
    }
}
