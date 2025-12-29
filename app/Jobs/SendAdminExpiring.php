<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminExpiringNotification;

class SendAdminExpiring implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $expiringBgs;

    /**
     * Create a new job instance.
     *
     * @param $expiringBgs
     */
    public function __construct($expiringBgs)
    {
        // Menyimpan data collection BG yang expired
        $this->expiringBgs = $expiringBgs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Email tujuan hardcoded sesuai request (Firas)
        $adminEmail = 'firas@admin.com';

        Mail::to($adminEmail)->send(new AdminExpiringNotification($this->expiringBgs));
    }
}
