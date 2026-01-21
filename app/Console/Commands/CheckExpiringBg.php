<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgRecommendation;
use App\Models\BG\Tax;
use App\Models\User;
use App\Mail\AdminExpiringNotification;
use App\Mail\SuratDistributorMail;
use App\Mail\SuratBankMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\DocumentHelper;
use Illuminate\Support\Facades\URL;
// [BARU] Import untuk Notifikasi Sistem
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class CheckExpiringBg extends Command
{
    protected $signature = 'bg:check-expired';
    protected $description = 'Cek BG expired H-60, create draft, catat log activity, dan kirim notifikasi.';

    public function handle()
    {
        $targetDate = Carbon::now()->addDays(60)->format('Y-m-d');
        $this->info("Checking BG expiring on: " . $targetDate . " (H-60)");

        $expiringBgs = BankGaransi::with('customer')
            ->whereDate('exp_date', $targetDate)
            ->where('status', 'approved')
            ->get();

        if ($expiringBgs->count() > 0) {
            DB::beginTransaction();
            try {
                $taxConfig = Tax::first();
                $taxId     = $taxConfig ? $taxConfig->id : null;
                $inflationFixed = 130;

                $delayCounter = 5;


                $internalEmails = User::role(['super-admin', 'manager-finance'])->pluck('email')->toArray();
                $internalEmails = array_unique(array_filter($internalEmails));

                $internalUsers = User::role(['super-admin', 'manager-finance', 'head-finance'])->get();

                if (!empty($internalEmails)) {
                    Mail::to($internalEmails)->later(
                        now()->addSeconds($delayCounter),
                        new AdminExpiringNotification($expiringBgs)
                    );
                    $delayCounter += 5;
                }

                foreach($expiringBgs as $bg) {
                    $cust = $bg->customer;
                    if(!$cust) continue;

                    $exists = BgRecommendation::where('customer_id', $cust->id)
                                ->where('status', 'pending')
                                ->where('created_at', '>=', Carbon::now()->subDays(1))
                                ->first();

                    if(!$exists) {
                        $top      = $cust->term_of_payment ?? 0;
                        $leadTime = $cust->lead_time ?? 0;

                        $newRec = BgRecommendation::create([
                            'customer_id'       => $cust->id,
                            'tax_id'            => $taxId,
                            'top'               => $top,
                            'lead_time'         => $leadTime,
                            'current_bg'        => $bg->bg_nominal,
                            'inflation'         => $inflationFixed,
                            'status'            => 'pending'
                        ]);
                        $bg->temp_recommendation_id = $newRec->id;
                    } else {
                        $bg->temp_recommendation_id = $exists->id;
                    }

                    try {
                        activity()
                            ->useLog('system_alert')
                            ->performedOn($bg)
                            ->withProperties([
                                'bg_number' => $bg->bg_number,
                                'customer'  => $cust->name,
                                'exp_date'  => $bg->exp_date,
                                'action'    => 'H-60 Notification Sent'
                            ])
                            ->log("System Warning: Bank Garansi {$bg->bg_number} Expired H-60 pada tanggal " . $targetDate);
                    } catch (\Exception $logEx) {
                        $this->error("Gagal mencatat log: " . $logEx->getMessage());
                    }

                    if ($internalUsers->count() > 0) {
                        Notification::send($internalUsers, new SystemNotification(
                            'BG Expiring Soon (H-60)', // Judul
                            "BG No: <b>{$bg->bg_number}</b> milik <b>{$cust->name}</b> akan expired pada <b>" . date('d M Y', strtotime($targetDate)) . "</b>.",
                            route('bg-list.index'), // URL Redirect saat diklik (Ke List BG)
                            'ph-clock-warning', // Icon
                            'danger' // Warna Merah
                        ));
                    }

                    if ($cust->email) {
                        $nomorPkd = DocumentHelper::generatePKDNumber($bg->temp_recommendation_id, $cust->name, now());

                        $dataPdf = [
                            'customer'      => $cust,
                            'bg'            => $bg,
                            'nomor_pkd'     => $nomorPkd,
                            'expired_date'  => $bg->exp_date,
                            'bank_name'     => $bg->bank_name ?? 'Bank',
                            'branch_name'   => $bg->branch_name ?? 'SMII Office',
                            'bank_address'  => $bg->bank_address ?? $bg->branch_name ?? 'KCU Sudirman',
                            'nominal'       => $bg->bg_nominal
                        ];

                        $linkDistributor = URL::temporarySignedRoute(
                            'public.bg.download', now()->addDays(7),
                            ['bg_id' => $bg->id, 'type' => 'distributor']
                        );

                        $linkBank = URL::temporarySignedRoute(
                            'public.bg.download', now()->addDays(7),
                            ['bg_id' => $bg->id, 'type' => 'bank']
                        );

                        Mail::to($cust->email)->later(
                            now()->addSeconds($delayCounter),
                            new SuratDistributorMail($cust, $dataPdf, $linkDistributor)
                        );
                        $delayCounter += 5;

                        Mail::to($cust->email)->later(
                            now()->addSeconds($delayCounter),
                            new SuratBankMail($cust, $dataPdf, $linkBank)
                        );
                        $delayCounter += 5;

                        $this->info("Notifications scheduled for Customer: {$cust->name}");
                    }
                }

                DB::commit();
                $this->info("Proses selesai. {$expiringBgs->count()} BG diproses.");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error: " . $e->getMessage());
                activity()->useLog('system_error')->log('Scheduler Error: ' . $e->getMessage());
            }
        } else {
            $this->info("Tidak ada BG yang expired tepat H-60 hari ini.");
        }
    }
}
