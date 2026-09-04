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

        // Logic Perhitungan:
        // 1. BG yang expired hari ini atau sudah jatuh tempo (exp_date <= today)
        // 2. Batch bulanan 60-90 hari ke depan (H-60 dan H-90):
        //    a. Batch A: 2 bulan ke depan (addMonths(2)), khusus tanggal 1 s/d 15
        //    b. Batch B: 3 bulan ke depan (addMonths(3)), khusus tanggal 16 s/d 31
        $targetMonth1 = $today->copy()->addMonths(2); // Untuk batch tanggal 1-15 (H-60)
        $targetMonth2 = $today->copy()->addMonths(3); // Untuk batch tanggal > 15 (H-90)

        $expiringBgs = BankGaransi::with('customer')
            ->where('status', 'approved')
            ->where(function($query) use ($todayDate, $targetMonth1, $targetMonth2) {
                // 1. BG yang expired hari ini atau sudah lewat jatuh tempo
                $query->whereDate('exp_date', '<=', $todayDate)
                // 2. Batch A: Tanggal 1-15 (Target 2 bulan ke depan)
                ->orWhere(function($q) use ($targetMonth1) {
                    $q->whereMonth('exp_date', $targetMonth1->month)
                      ->whereYear('exp_date', $targetMonth1->year)
                      ->whereDay('exp_date', '<=', 15);
                })
                // 3. Batch B: Tanggal >15 (Target 3 bulan ke depan)
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

                    if ($cust->email) {
                        $nomorPkd = DocumentHelper::generatePKDNumber($bg->temp_recommendation_id, $cust->name, now());

                        $financeUser = User::role('manager-finance')->first() ?? User::role('head-finance')->first();
                        $financeName = $financeUser ? $financeUser->name : 'Manager Finance';

                        $dataPdf = [
                            'customer'      => $cust,
                            'bg'            => $bg,
                            'nomor_pkd'     => $nomorPkd,
                            'expired_date'  => $bg->exp_date,
                            'bank_name'     => $bg->bank_name ?? '-',
                            'branch_name'   => $bg->branch_name ?? '-',
                            'bank_address'  => $bg->bank_address ?? $bg->branch_name ?? '-',
                            'nominal'       => $bg->bg_nominal,
                            'finance_name'  => $financeName,
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
            $this->info("There are no BGs that are expired today or fall into the early month calculation batch.");
        }
    }
}