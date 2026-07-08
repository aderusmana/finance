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
            Mail::to($this->email)->send(new LogisticOrderDistributorMail($this->order, $this->type));
        } catch (\Exception $e) {
            Log::error("Failed to send logistic order email to {$this->email}: " . $e->getMessage());
        }
    }
}