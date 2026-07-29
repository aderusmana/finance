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
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class CheckExpiringBg extends Command
{
    protected $signature = 'bg:check-expired';
    protected $description = 'Check BG and send recommendation notifications at the beginning of the month according to the date batch.';

    public function handle()
    {
        $today = Carbon::now();

        // 1. Pastikan command hanya mengeksekusi data pada tanggal 1 setiap bulannya
        if ($today->day !== 1) {
            $this->info("No execution. BG recommendation notifications are only sent on the 1st of every month.");
            return;
        }

        $this->info("Checking BG recommendation notifications for period: " . $today->format('F Y'));

        // 2. Logic Perhitungan Batch (Reverse Lookup):
        // - Jika target kirim adalah Hari Ini (Bulan Y), maka BG yang ditarik adalah:
        //   a. BG dari 2 bulan yang lalu (Bulan Y - 2), khusus tanggal 1 s/d 15
        //   b. BG dari 3 bulan yang lalu (Bulan Y - 3), khusus tanggal 16 s/d 31
        
        $targetMonth1 = $today->copy()->subMonths(2); // Untuk batch tanggal 1-15
        $targetMonth2 = $today->copy()->subMonths(3); // Untuk batch tanggal > 15

        $expiringBgs = BankGaransi::with('customer')
            ->where('status', 'approved')
            ->where(function($query) use ($targetMonth1, $targetMonth2) {
                // Batch A: Tanggal 1-15 (Target - 2 bulan)
                // Contoh: Kirim 1 September -> Ditarik BG tanggal 1-15 Juli
                $query->where(function($q) use ($targetMonth1) {
                    $q->whereMonth('exp_date', $targetMonth1->month)
                      ->whereYear('exp_date', $targetMonth1->year)
                      ->whereDay('exp_date', '<=', 15);
                })
                // Batch B: Tanggal >15 (Target - 3 bulan)
                // Contoh: Kirim 1 Oktober -> Ditarik BG tanggal 16-31 Juli
                ->orWhere(function($q) use ($targetMonth2) {
                    $q->whereMonth('exp_date', $targetMonth2->month)
                      ->whereYear('exp_date', $targetMonth2->year)
                      ->whereDay('exp_date', '>', 15);
                });
            })
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
                                'action'    => 'Monthly Batch Notification Sent'
                            ])
                            ->log("System Warning: A bank guarantee notification {$bg->bg_number} was sent at the beginning of the month " . $today->format('F Y'));
                    } catch (\Exception $logEx) {
                        $this->error("Failed to record log: " . $logEx->getMessage());
                    }

                    if ($internalUsers->count() > 0) {
                        Notification::send($internalUsers, new SystemNotification(
                            'BG Recommendation (Start of Month)', 
                            "Recommendation BG No: <b>{$bg->bg_number}</b> from <b>{$cust->name}</b> was processed at the beginning of this month.",
                            route('bg-list.index'), 
                            'ph-clock-warning', 
                            'danger' 
                        ));
                    }

                    if ($cust->email) {
                        $nomorPkd = DocumentHelper::generatePKDNumber($bg->temp_recommendation_id, $cust->name, now());

                        $dataPdf = [
                            'customer'      => $cust,
                            'bg'            => $bg,
                            'nomor_pkd'     => $nomorPkd,
                            'expired_date'  => $bg->exp_date,
                            'bank_name'     => $bg->bank_name ?? '-',
                            'branch_name'   => $bg->branch_name ?? '-',
                            'bank_address'  => $bg->bank_address ?? $bg->branch_name ?? '-',
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
                $this->info("Process completed. {$expiringBgs->count()} BGs processed.");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error: " . $e->getMessage());
                activity()->useLog('system_error')->log('Scheduler Error: ' . $e->getMessage());
            }
        } else {
            $this->info("There are no BGs that fall into the early month calculation batch this month.");
        }
    }
}