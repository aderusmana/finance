<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Customer\Customer;
use App\Models\User;
use App\Mail\CustomerMail;

class CustomerJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $customerId;
    public $recipients;
    public $token;
    public $mailType; // approval, completed, rejected

    public function __construct(int $customerId, array $recipients = [], ?string $token = null, string $mailType = 'approval')
    {
        $this->customerId = $customerId;
        $this->recipients = $recipients;
        $this->token = $token;
        $this->mailType = $mailType;
    }

    public function handle()
    {
        try {
            $customer = Customer::find($this->customerId);
            if (! $customer) return;

            foreach ($this->recipients as $r) {
                try {
                    $email = $r['email'] ?? null;
                    if (! $email) continue;

                    // Cari User object jika ada NIK (untuk approver)
                    $approver = null;
                    if (!empty($r['nik'])) {
                        $approver = User::where('nik', $r['nik'])->first();
                    }

                    $data = [
                        'mail_type' => $this->mailType,
                        'token' => $this->token,
                        'approver_name' => $r['name'] ?? 'User',
                        'is_it'         => $r['is_it'] ?? false,
                    ];

                    Log::info("QUEUE WORKER: Mengirim email tipe '{$this->mailType}' untuk Customer #{$this->customerId} ke: {$email}");

                    Mail::to($email)->send(new CustomerMail($customer, $approver, $data));

                } catch (\Exception $e) {
                    Log::error('Mail Error: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Job Error: ' . $e->getMessage());
        }
    }
}
