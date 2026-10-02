<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgRecommendation;
use App\Models\BG\Tax;
use App\Models\User;
use App\Mail\AdminExpiringNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class CheckExpiringBg extends Command
{
    protected $signature = 'bg:check-expired {--force : Force execution regardless of day of month}';
    protected $description = 'Check BG expired today and send recommendation notifications according to batch.';

    public function handle()
    {
        $today = Carbon::now();
        $todayDate = $today->toDateString();

        $this->info("Checking Bank Guarantee expiry for: " . $today->format('d F Y'));

        // Aturan Pengecekan Akhir Bulan (Horizon 2 Bulan ke Depan):
        // 1. BG yang expired hari ini atau sudah jatuh tempo (exp_date <= today)
        // 2. Horizon 2 bulan ke depan:
        //    - Tanggal 1 s/d 15 di 2 bulan ke depan (Bulan M + 2): Masuk ke perhitungan bulan sekarang (Bulan M)
        //    - Tanggal 16 s/d akhir bulan di 2 bulan ke depan (Bulan M + 2): Masuk ke perhitungan bulan depannya (Bulan M + 1)
        //    - Tanggal 16 s/d akhir bulan di 1 bulan ke depan (Bulan M + 1): Masuk ke perhitungan bulan sekarang (Bulan M)
        $monthPlus1 = $today->copy()->addMonths(1);
        $monthPlus2 = $today->copy()->addMonths(2);

        $expiringBgs = BankGaransi::with('customer')
            ->where('status', 'approved')
            ->where(function($query) use ($todayDate, $monthPlus1, $monthPlus2) {
                // 1. BG yang expired hari ini atau sudah lewat jatuh tempo
                $query->whereDate('exp_date', '<=', $todayDate)
                // 2. Tanggal 1-15 di 2 bulan ke depan (masuk ke bulan sekarang)
                ->orWhere(function($q) use ($monthPlus2) {
                    $q->whereMonth('exp_date', $monthPlus2->month)
                      ->whereYear('exp_date', $monthPlus2->year)
                      ->whereDay('exp_date', '<=', 15);
                })
                // 3. Tanggal 16-akhir bulan di 1 bulan ke depan (limpahan bulan lalu, masuk ke bulan sekarang)
                ->orWhere(function($q) use ($monthPlus1) {
                    $q->whereMonth('exp_date', $monthPlus1->month)
                      ->whereYear('exp_date', $monthPlus1->year)
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

                $hasAdminRtm = Role::where('name', 'admin-rtm')->exists();
                $internalEmails = $hasAdminRtm
                    ? User::role('admin-rtm')->pluck('email')->toArray()
                    : User::role(['manager-finance'])->pluck('email')->toArray();
                $internalEmails = array_unique(array_filter($internalEmails));

                $targetRoles = array_values(array_filter(
                    ['admin-rtm', 'manager-finance', 'head-finance'],
                    fn($r) => Role::where('name', $r)->exists()
                ));
                $internalUsers = !empty($targetRoles) ? User::role($targetRoles)->get() : collect();

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
                                'action'    => 'BG Expired / Batch Notification Sent'
                            ])
                            ->log("System Warning: A bank guarantee notification {$bg->bg_number} from {$cust->name} was sent for period " . $today->format('F Y'));
                    } catch (\Exception $logEx) {
                        $this->error("Failed to record log: " . $logEx->getMessage());
                    }

                    if ($internalUsers->count() > 0) {
                        Notification::send($internalUsers, new SystemNotification(
                            'BG Recommendation (Expired / Expiring)', 
                            "Recommendation BG No: <b>{$bg->bg_number}</b> from <b>{$cust->name}</b> was processed.",
                            route('bg-recommendations.index'), 
                            'ph-clock-warning', 
                            'danger' 
                        ));
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
            $this->info("There are no BGs that are expired today or fall into the early month calculation batch.");
        }
    }
}