<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class LogisticOrderDistributorMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $urlDetail;
    public $urlDownload;
    public $type;

    public function __construct($order, $type = 'distributor')
    {
        $this->order = $order;
        $this->type = $type;
        $this->urlDetail = URL::signedRoute('public.lo.detail', ['id' => $order->id]);
        $this->urlDownload = URL::signedRoute('public.lo.download', ['id' => $order->id, 'fromEmail' => true]);
    }

    public function build()
    {
        $formattedLo = 'LO-' . str_pad($this->order->logistic_order_no, 4, '0', STR_PAD_LEFT);

        if ($this->type === 'sales') {
            return $this->subject('Notifikasi Download Delivery Notes: ' . $formattedLo)
                        ->view('mail.distributor_order');
        }

        return $this->subject('Pemberitahuan Logistic Order Baru: ' . $formattedLo)
                    ->view('mail.distributor_order');
    }
}