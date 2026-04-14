<?php
namespace App\Jobs;

use App\Mail\LogisticFeeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLogisticFee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $log;
    protected $logisticData;

    public function __construct($log, $logisticData)
    {
        $this->log = $log;
        $this->logisticData = $logisticData;
    }

    public function handle()
    {
        $approver = User::where('nik', $this->log->approver_nik)->first();

        if ($approver && $approver->email) {
            
            Mail::to($approver->email)->send(new LogisticFeeMail(
                'request',
                $this->logisticData, 
                [
                    'log' => $this->log, 
                    'approverName' => $approver->name
                ]
            ));
            
        }
    }
}
