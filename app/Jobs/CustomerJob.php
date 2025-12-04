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
    public $recipients; // array of ['nik','email','name','level','is_first']
    public $token;
    public $mailType;

    public function __construct(int $customerId, array $recipients = [], ?string $token = null, string $mailType = 'approval')
    {
        $this->customerId = $customerId;
        $this->recipients = $recipients;
        $this->token = $token;
        $this->mailType = $mailType;

        // Job should be queued; configuration controls queue connection
        // $this->onQueue('emails');
    }

    public function handle()
    {
        try {
            $customer = Customer::find($this->customerId);
            if (! $customer) {
                Log::warning('CustomerJob: customer not found', ['customer_id' => $this->customerId]);
                return;
            }

            foreach ($this->recipients as $r) {
                try {
                    $email = $r['email'] ?? null;
                    $name = $r['name'] ?? null;
                    $nik = $r['nik'] ?? null;
                    $level = $r['level'] ?? null;
                    $isFirst = $r['is_first'] ?? false;

                    if (! $email) continue;

                    // If possible, retrieve fresh user model
                    $approver = null;
                    if (!empty($nik)) {
                        $approver = User::where('nik', $nik)->first();
                    }

                    $data = [
                        'mail_type' => $this->mailType,
                        'token' => $this->token,
                        'level' => $level,
                        'is_first' => $isFirst,
                        'approver_name' => $name,
                    ];

                    Mail::to($email)->send(new CustomerMail($customer, $approver, $data));
                } catch (\Exception $e) {
                    Log::error('CustomerJob: error sending mail to recipient', ['recipient' => $r, 'error' => $e->getMessage()]);
                }
            }
        } catch (\Exception $e) {
            Log::error('CustomerJob: unexpected error', ['error' => $e->getMessage()]);
        }
    }
}
