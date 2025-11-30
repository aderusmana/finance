<?php

namespace App\Mail;

use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class MailFreeGoods extends Mailable
{
    use Queueable, SerializesModels;

    public $requisition;
    public $recipient;
    public $data;

    public function __construct(Requisition $requisition, User $recipient, array $data = [])
    {
        $this->requisition = $requisition;
        $this->recipient = $recipient;
        $this->data = $data;

        // Menambahkan recipient ke dalam data agar bisa diakses di view
        $this->data['recipient'] = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        // Default subject
        $subject = 'Request Requisition Free Goods: ' . $this->requisition->no_srs;

        // Mengubah subject berdasarkan tipe email dari data
        if (isset($this->data['mail_type'])) {
            switch ($this->data['mail_type']) {
                case 'warehouse_process':
                    $step = $this->data['process_step'] ?? 'Warehouse Process';
                    $subject = "{$step} for SRS: {$this->requisition->no_srs}";
                    break;
                case 'completed_notification':
                    $subject = 'Completed: Your Free Goods Requisition ' . $this->requisition->no_srs . ' is Ready';
                    break;
                case 'rejection_notification':
                    $subject = 'Rejected: Your Free Goods Requisition ' . $this->requisition->no_srs;
                    break;
                case 'cancellation_notification':
                    $subject = 'Cancelled: Free Goods Requisition ' . $this->requisition->no_srs . ' has been cancelled';
                    break;
            }
        }

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $subject,
        );
    }

    public function content()
    {
        return new Content(
            view: 'mail.mail-freegoods',
            // Kita tetap meneruskan $data agar variabel lain seperti URL tetap ada
            with: $this->data
        );
    }

    public function attachments()
    {
        return [];
    }
}