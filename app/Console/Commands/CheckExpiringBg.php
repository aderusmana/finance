<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgRecommendation;
use App\Models\BG\Tax;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminExpiringNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckExpiringBg extends Command
{
    protected $signature = 'bg:check-expired';
    protected $description = 'Cek BG expired, create draft rekomendasi (tanpa rule percent), kirim email';

    public function handle()
    {
        $today = Carbon::now()->startOfDay(); // Hari ini (00:00:00)
        $upcomingDate = Carbon::now()->addDays(60)->endOfDay(); // 60 hari lagi (23:59:59)
        
        $expiringBgs = BankGaransi::with('customer')
        ->whereBetween('exp_date', [$today, $upcomingDate])
        ->where('status', 'sent_to_customer')
        ->get();

        if ($expiringBgs->count() > 0) {
            DB::beginTransaction();
            try {
                // Config
                $taxConfig = Tax::first();
                $taxId     = $taxConfig ? $taxConfig->id : null;
                $inflationFixed = 130;

                foreach($expiringBgs as $bg) {
                    $cust = $bg->customer;
                    $top      = $cust->term_of_payment ?? 0;
                    $leadTime = $cust->lead_time ?? 0;

                    // Create Recommendation Awal
                    BgRecommendation::create([
                        'customer_id'       => $cust->id,
                        'tax_id'            => $taxId,
                        'top'               => $top,     
                        'lead_time'         => $leadTime,
                        'current_bg'        => $bg->bg_nominal,
                        'inflation'         => $inflationFixed,
                        'average'           => 0, 
                        'recommended_credit_limit' => 0, 
                        'rounded_credit_limit'     => 0,
                        'fk_with_limit'            => 0,
                        'set_bg'                   => 0, 
                        'credit_limit_updated'     => 0,
                        
                        'status' => 'pending',
                        'notes'  => 'Auto-generated on: ' . Carbon::now()->format('d M Y'),
                    ]);
                }

                // Kirim Email
                Mail::to('firas@admin.com')->send(new AdminExpiringNotification($expiringBgs));
                
                DB::commit();
                $this->info("Success! {$expiringBgs->count()} BG diproses.");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error: " . $e->getMessage());
            }

        } else {
            $this->info('Tidak ada BG expired.');
        }
    }
}