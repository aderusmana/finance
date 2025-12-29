<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerFillFormNotification;
use App\Models\BG\BgRecommendation;

class SendCustomerRecommendation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recommendation;

    /**
     * Create a new job instance.
     *
     * @param BgRecommendation $recommendation
     */
    public function __construct(BgRecommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->recommendation->customer && $this->recommendation->customer->email) {
            Mail::to($this->recommendation->customer->email)
                ->send(new CustomerFillFormNotification($this->recommendation));
        }
    }
}
