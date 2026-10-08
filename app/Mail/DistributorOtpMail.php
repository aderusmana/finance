<?php

namespace App\Mail;

use App\Models\Customer\Distributor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DistributorOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public Distributor $distributor;
    public string $otp;
    public int $expiresMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(Distributor $distributor, string $otp, int $expiresMinutes = 5)
    {
        $this->distributor = $distributor;
        $this->otp = $otp;
        $this->expiresMinutes = $expiresMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi OTP - Portal Dokumen Distributor (' . $this->distributor->code . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.distributor-otp',
            with: [
                'distributor' => $this->distributor,
                'otp' => $this->otp,
                'expiresMinutes' => $this->expiresMinutes,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
