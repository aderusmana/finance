<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RequisitionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $data;
    private $causerName; // <-- [FIX] Properti baru untuk menyimpan nama

    /**
     * Buat instance notifikasi baru.
     *
     * @param array $data Data notifikasi
     * @param User $causer User yang memicu notifikasi
     */
    public function __construct($data, User $causer) // <-- [FIX] Tambahkan parameter $causer
    {
        $this->data = $data;
        $this->causerName = $causer->name; // <-- [FIX] Simpan nama user saat notifikasi dibuat
    }

    /**
     * Tentukan channel pengiriman notifikasi.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Format data yang akan disimpan ke database.
     */
    public function toArray($notifiable)
    {
        return [
            'requisition_id' => $this->data['requisition_id'],
            'srs_number'     => $this->data['srs_number'],
            'message'        => $this->data['message'],
            'url'         => $this->data['url'],
            'causer_name'    => $this->causerName,
        ];
    }
}