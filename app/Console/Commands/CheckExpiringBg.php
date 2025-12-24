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

class CheckExpiringBg extends Command
{
    protected $signature = 'bg:check-expired';
    protected $description = 'Cek BG expired, create draft, kirim notifikasi terpisah dengan jeda';

    public function handle()
    {
        $today = Carbon::now()->startOfDay(); 
        $upcomingDate = Carbon::now()->addDays(60)->endOfDay(); 

        $expiringBgs = BankGaransi::with('customer')
            ->whereBetween('exp_date', [$today, $upcomingDate])
            ->where('status', 'approved')
            ->get();

        if ($expiringBgs->count() > 0) {
            DB::beginTransaction();
            try {
                $taxConfig = Tax::first();
                $taxId     = $taxConfig ? $taxConfig->id : null;
                $inflationFixed = 130;

                $delayCounter = 5; 

                // 1. BUAT DRAFT RECOMMENDATION
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
                            'status'            => 'pending',
                            'notes'             => null
                        ]);
                        $bg->temp_recommendation_id = $newRec->id;
                    } else {
                        $bg->temp_recommendation_id = $exists->id;
                    }
                }

                // 2. KIRIM LIST KE ADMIN (Prioritas Pertama)
                $internalEmails = User::role(['super-admin', 'manager-finance'])->pluck('email')->toArray();
                $internalEmails = array_unique(array_filter($internalEmails));

                if (!empty($internalEmails)) {
                    // Gunakan later() dengan waktu sekarang + delayCounter
                    Mail::to($internalEmails)->later(now()->addSeconds($delayCounter), new AdminExpiringNotification($expiringBgs));
                    
                    // Tambahkan jeda 5 detik untuk email berikutnya
                    $delayCounter += 5; 
                }

                // 3. KIRIM SURAT KE CUSTOMER
                foreach ($expiringBgs as $bg) {
                    $cust = $bg->customer;
                    
                    if ($cust && $cust->email) {
                        $nomorPkd = DocumentHelper::generatePKDNumber($bg->temp_recommendation_id, $cust->name, now());
                        
                        $dataPdf = [
                            'customer' => $cust, 
                            'bg' => $bg,
                            'nomor_pkd' => $nomorPkd,
                            'expired_date' => $bg->exp_date,
                            'bank_name' => $bg->bank_name ?? 'Bank',
                            'branch_name' => $bg->branch_name ?? 'SMII Office',
                            'bank_address' => $bg->bank_address ?? $bg->branch_name ?? 'KCU Sudirman    ',
                            'nominal' => $bg->bg_nominal
                        ];

                        $linkDistributor = URL::temporarySignedRoute(
                            'public.bg.download', now()->addDays(7), 
                            ['bg_id' => $bg->id, 'type' => 'distributor']
                        );

                        $linkBank = URL::temporarySignedRoute(
                            'public.bg.download', now()->addDays(7), 
                            ['bg_id' => $bg->id, 'type' => 'bank']
                        );

                        // A. SURAT DISTRIBUTOR
                        Mail::to($cust->email)->later(
                            now()->addSeconds($delayCounter), 
                            new SuratDistributorMail($cust, $dataPdf, $linkDistributor) // Pass Link
                        );
                        $delayCounter += 5;

                        // B. SURAT BANK
                        Mail::to($cust->email)->later(
                            now()->addSeconds($delayCounter), 
                            new SuratBankMail($cust, $dataPdf, $linkBank) // Pass Link
                        );
                        $delayCounter += 5;
                    }
                }

                DB::commit();
                $this->info("Proses selesai. {$expiringBgs->count()} BG diproses. Email dijadwalkan dengan jeda 5 detik.");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error: " . $e->getMessage());
            }
        } else {
            $this->info("Tidak ada BG expired.");
        }
    }
}