<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer\LogisticOrder;
use App\Jobs\SendLogisticOrderEmailJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendLogisticOrderReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logistic-order:send-reminder {--dry-run : Simulate reminder checks without actually sending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily email reminders to distributors for pending logistic orders (every 5 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $today = Carbon::today();

        $this->info("Checking Logistic Order reminders (" . ($isDryRun ? "DRY-RUN MODE" : "ACTUAL MODE") . ")...");

        // Fetch orders matching criteria:
        // 1. Not canceled (canceled_at is null)
        // 2. Delivery order note status is 'Pending Download'
        $orders = LogisticOrder::with(['distributor', 'customer', 'customerShipTo', 'note', 'items'])
            ->whereNull('canceled_at')
            ->whereHas('note', function ($query) {
                $query->where('status', 'Pending Download');
            })
            ->get();

        $eligibleCount = 0;
        $sentCount = 0;

        foreach ($orders as $order) {
            $loNo = 'LO-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);

            // Ensure distributor email exists
            if (!$order->distributor || empty($order->distributor->email_list)) {
                $this->warn("Order {$loNo}: Distributor has no email address. Skipping.");
                continue;
            }

            $shouldSend = false;
            $reason = '';

            if (is_null($order->last_reminder_at)) {
                // First Reminder: 5 days from creation date
                $createdDate = Carbon::parse($order->created_at)->startOfDay();
                $targetDate = $createdDate->copy()->addDays(5);

                if ($today->greaterThanOrEqualTo($targetDate)) {
                    $shouldSend = true;
                    $diffDays = $createdDate->diffInDays($today);
                    $reason = "Reminder #1 (H+{$diffDays} since created on {$createdDate->format('d/m/Y')})";
                }
            } else {
                // Subsequent Reminders: 5 days from last reminder date
                $lastReminderDate = Carbon::parse($order->last_reminder_at)->startOfDay();
                $targetDate = $lastReminderDate->copy()->addDays(5);

                if ($today->greaterThanOrEqualTo($targetDate)) {
                    $shouldSend = true;
                    $diffDays = $lastReminderDate->diffInDays($today);
                    $nextSeq = ($order->reminder_count ?? 0) + 1;
                    $reason = "Reminder #{$nextSeq} (H+{$diffDays} since last reminder on {$lastReminderDate->format('d/m/Y')})";
                }
            }

            if ($shouldSend) {
                $eligibleCount++;
                $recipients = implode(', ', $order->distributor->email_list);

                if ($isDryRun) {
                    $this->line(" [DRY-RUN] Ready to send {$reason} for {$loNo} to: {$recipients}");
                } else {
                    try {
                        dispatch(new SendLogisticOrderEmailJob($order, $order->distributor->email_list, 'reminder'));

                        $order->update([
                            'last_reminder_at' => now(),
                            'reminder_count' => ($order->reminder_count ?? 0) + 1,
                        ]);

                        $sentCount++;
                        $this->info(" [SENT] {$reason} for {$loNo} successfully sent to: {$recipients}");
                    } catch (\Exception $e) {
                        $this->error(" [FAILED] Failed to send reminder for {$loNo}: " . $e->getMessage());
                        Log::error("SendLogisticOrderReminder error for {$loNo}: " . $e->getMessage());
                    }
                }
            }
        }

        if ($isDryRun) {
            $this->info("Check completed. Found {$eligibleCount} orders eligible for reminder.");
        } else {
            $this->info("Process completed. Successfully sent {$sentCount} reminder emails to distributors.");
            Log::info("SendLogisticOrderReminder completed: {$sentCount} reminders sent.");
        }

        return 0;
    }
}
