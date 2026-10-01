<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\LogisticOrderDistributorMail;
use Illuminate\Support\Facades\Log;

class SendLogisticOrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $email;
    protected $type;

    public function __construct($order, $email, $type = 'distributor')
    {
        $this->order = $order;
        $this->email = $email;
        $this->type = $type;
    }

    public function handle()
    {
        try {
            $recipients = is_array($this->email)
                ? $this->email
                : array_filter(array_map('trim', preg_split('/[;,]+/', (string)$this->email)));

            $validRecipients = [];
            foreach ($recipients as $item) {
                $cleaned = trim((string)$item);
                if (filter_var($cleaned, FILTER_VALIDATE_EMAIL) && !in_array($cleaned, $validRecipients)) {
                    $validRecipients[] = $cleaned;
                }
            }

            if (!empty($validRecipients)) {
                Mail::to($validRecipients)->send(new LogisticOrderDistributorMail($this->order, $this->type));
            } else {
                $orig = is_array($this->email) ? implode(', ', $this->email) : $this->email;
                Log::warning("SendLogisticOrderEmailJob: Tidak ada alamat email yang valid untuk Logistic Order #{$this->order->id}. Input: '{$orig}'");
            }
        } catch (\Exception $e) {
            $dest = is_array($this->email) ? implode(', ', $this->email) : $this->email;
            Log::error("Failed to send logistic order email to {$dest}: " . $e->getMessage());
        }
    }
}