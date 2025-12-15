<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BG\BankGaransi;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminExpiringNotification;
use Carbon\Carbon;

class CheckExpiringBg extends Command
{
    // Nama command yang nanti dipanggil oleh sistem
    protected $signature = 'bg:check-expired';

    // Deskripsi command
    protected $description = 'Cek Bank Garansi yang akan expired dalam 60 hari dan kirim email ke Firas';

    public function handle()
    {
        // 1. Tentukan tanggal target (Hari ini + 60 hari)
        $targetDate = Carbon::now()->addDays(60)->format('Y-m-d');

        // 2. Query ke Database: Cari BG yang statusnya 'approved' DAN exp_date == targetDate
        $expiringBgs = BankGaransi::with('customer')
            ->whereDate('exp_date', $targetDate)
            ->where('status', 'approved')
            ->get();

        // 3. Cek apakah ada data?
        if ($expiringBgs->count() > 0) {
            // Jika ADA, kirim email ke Firas
            Mail::to('firas@admin.com')->send(new AdminExpiringNotification($expiringBgs));

            $this->info("Email berhasil dikirim! Ada {$expiringBgs->count()} customer yang akan expired.");
        } else {
            // Jika TIDAK ADA, diam saja (tidak perlu spam email kosong)
            $this->info("Tidak ada data yang expired pada tanggal $targetDate.");
        }
    }
}
