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

class mailSample extends Mailable
{
    use Queueable, SerializesModels;

    public $requisition;
    public $recipient; // Menggunakan nama generik 'recipient'
    public $data;

    /**
     * Kita tambahkan $mailType untuk menentukan jenis email
     */
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
        $subject = 'Request Requisition Sample: ' . $this->requisition->no_srs;

        // Mengubah subject berdasarkan tipe email dari data
        if (isset($this->data['mail_type'])) {
            switch ($this->data['mail_type']) {
                case 'warehouse_process':
                    $step = $this->data['process_step'] ?? 'Warehouse Process';
                    $subject = "{$step} for SRS: {$this->requisition->no_srs}";
                    break;
                case 'completed_notification':
                    $subject = 'Completed: Your Sample Requisition ' . $this->requisition->no_srs . ' is Ready';
                    break;
                case 'rejection_notification':
                    $subject = 'Rejected: Your Sample Requisition ' . $this->requisition->no_srs;
                    break;
                case 'recallation_notification':
                    $subject = 'Recalled: Sample Requisition ' . $this->requisition->no_srs . ' has been recalled';
                    break;
            }
        }

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new Content(
            view: 'mail.mail-sample',
            with: $this->data
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments()
    {
        return [];
    }
}
