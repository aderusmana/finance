<?php

namespace App\Jobs;

use App\Mail\mailSample;
use App\Models\Requisition\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendSample implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // [MODIFIKASI] Simpan ID, bukan model lengkap
    protected $requisitionId;
    protected $recipient;
    protected $token;
    protected $mailData;

    public function __construct($requisition, User $recipient, ?string $token, array $mailData = [])
    {
        // [MODIFIKASI] Ambil ID dari model
        $this->requisitionId = $requisition->id;
        $this->recipient = $recipient;
        $this->token = $token;
        $this->mailData = $mailData;
    }

    public function handle()
    {
        try {
            $requisition = Requisition::with('requester')->findOrFail($this->requisitionId);

            $mailType = $this->mailData['mail_type'] ?? 'approval';
            $dataForMail = $this->mailData;

            if ($mailType === 'approval') {
                $dataForMail['approve_url'] = route('approval.response', ['token' => $this->token, 'action' => 'approve']);
                $dataForMail['review_url']  = route('approval.response', ['token' => $this->token, 'action' => 'review']);
                $dataForMail['reject_url']  = route('approval.response', ['token' => $this->token, 'action' => 'reject']);
            } elseif ($mailType === 'warehouse_process') {
                $dataForMail['submit_url'] = route('approval.response', ['token' => $this->token, 'action' => 'submit']);
                $dataForMail['review_url'] = route('approval.response', ['token' => $this->token, 'action' => 'review']);
            }

            Mail::to($this->recipient->email)->send(new mailSample($requisition, $this->recipient, $dataForMail));

            Log::info("Email (Tipe: {$mailType}) untuk Requisition #{$requisition->id} berhasil dikirim ke {$this->recipient->email}.");

        } catch (\Exception $e) {
            Log::error("Gagal mengirim email untuk Requisition #{$requisition->id}. Error: " . $e->getMessage() . " on line " . $e->getLine());
        }
    }
}
