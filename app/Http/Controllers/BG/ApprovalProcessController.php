<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\Master\ApprovalLog;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerBgReadyMail;
use App\Models\BG\BgHistory;
use App\Models\BG\LampiranD;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApprovalProcessController extends Controller
{
    public function process($token, $action)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->first();

        if (!$log) {
            return view('page.customer_portal.form-invalid');
        }

        if ($action == 'approve') {
            $log->update([
                'status' => 'Approved',
                'updated_at' => now(),
                'token' => null
            ]);

            $this->finalizeSubmission($log->related_id);

            return view('page.customer_portal.form-success', ['type' => 'upload', 'title' => 'Approved Successfully']);
        }

        abort(404);
    }

    public function showForm($token, $action)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->first();

        if (!$log) {
            return view('page.customer_portal.form-invalid');
        }

        $submission = BgSubmission::with('recommendation.customer')->findOrFail($log->related_id);

        // --- [FIX START] LOGIC PENCARIAN BG (METADATA AWARE) ---
        $rec = $submission->recommendation;
        $metadata = json_decode($rec->notes, true) ?? [];
        $bg = null;

        // SKENARIO 1: EXISTING (Cari by ID dari Metadata)
        if (isset($metadata['action']) && $metadata['action'] === 'existing' && !empty($metadata['target_bg_id'])) {
            $bg = BankGaransi::with('details')->find($metadata['target_bg_id']);
        }
        // SKENARIO 2: NEW / EXTENSION (Cari by Timestamp)
        else {
            $createdAt = Carbon::parse($submission->created_at);
            $start = $createdAt->copy()->subMinutes(2); // Perlebar range sedikit
            $end   = $createdAt->copy()->addMinutes(2);

            $siblingSubmissions = BgSubmission::where('bg_recommendation_id', $submission->bg_recommendation_id)
                                    ->whereBetween('created_at', [$start, $end])
                                    ->orderBy('id', 'asc')
                                    ->pluck('id')
                                    ->toArray();

            $myIndex = array_search($submission->id, $siblingSubmissions);
            $candidateBgs = BankGaransi::where('customer_id', $submission->recommendation->customer_id)
                                ->whereBetween('created_at', [$start, $end])
                                ->with('details')
                                ->orderBy('id', 'asc')
                                ->get();

            if ($myIndex !== false && isset($candidateBgs[$myIndex])) {
                $bg = $candidateBgs[$myIndex];
            } else {
                $bg = $candidateBgs->first();
            }
        }
        // --- [FIX END] ---

        if (!$bg) {
             return abort(404, 'Data Bank Garansi tidak ditemukan. Kemungkinan Timestamp mismatch atau ID salah.');
        }

        $bgs = collect([$bg]);
        $totalBgDiserahkan = $bg->bg_nominal;

        return view('page.approval.action_lampiran', compact('token', 'action', 'submission', 'bgs', 'totalBgDiserahkan'));
    }

    public function submit(Request $request, $token)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->firstOrFail();

        $action = $request->action;
        $status = ($action == 'reject') ? 'Rejected' : 'Approved';

        $log->update([
            'status' => $status,
            'notes' => $request->notes,
            'updated_at' => now(),
            'token' => null
        ]);

        if ($status == 'Rejected') {
            $sub = BgSubmission::find($log->related_id);
            if ($sub) {
                $sub->update(['status' => 'rejected_by_finance']);
            }
        } else {
            $this->finalizeSubmission($log->related_id);
        }

        return view('page.customer_portal.form-success', ['type' => 'upload', 'title' => 'Processed Successfully']);
    }

    private function finalizeSubmission($submissionId) {
        $sub = BgSubmission::with(['recommendation.customer'])->find($submissionId);

        if($sub) {
            $sub->update([
                'status' => 'completed',
                'token' => Str::random(60),
                'reviewed_at' => now()
            ]);

            $financeUser = User::role(['manager-finance', 'head-finance'])->first();
            $approverId = $financeUser ? $financeUser->id : null;

            // --- [FIX START] LOGIC FINALIZE JUGA HARUS DIPERBAIKI ---
            $rec = $sub->recommendation;
            $metadata = json_decode($rec->notes, true) ?? [];
            $targetBg = null;

            // 1. Existing Check
            if (isset($metadata['action']) && $metadata['action'] === 'existing' && !empty($metadata['target_bg_id'])) {
                $targetBg = BankGaransi::find($metadata['target_bg_id']);
            }
            // 2. New/Extension Check
            else {
                $createdAt = Carbon::parse($sub->created_at);
                $start = $createdAt->copy()->subMinutes(2);
                $end   = $createdAt->copy()->addMinutes(2);

                $siblingSubmissions = BgSubmission::where('bg_recommendation_id', $sub->bg_recommendation_id)
                                        ->whereBetween('created_at', [$start, $end])
                                        ->orderBy('id', 'asc')
                                        ->pluck('id')->toArray();

                $myIndex = array_search($sub->id, $siblingSubmissions);

                $candidateBgs = BankGaransi::where('customer_id', $sub->recommendation->customer_id)
                                    ->whereBetween('created_at', [$start, $end])
                                    ->orderBy('id', 'asc')
                                    ->get();

                if ($myIndex !== false && isset($candidateBgs[$myIndex])) {
                    $targetBg = $candidateBgs[$myIndex];
                } else {
                    $targetBg = $candidateBgs->first();
                }
            }
            // --- [FIX END] ---

            if ($targetBg) {
                // Update BG jadi Approved
                $targetBg->update([
                    'status'      => 'approved',
                    'issued_date' => now(),
                    'exp_date'    => now()->addYear(),
                ]);

                // Cari BG Sebelumnya (Logic History)
                $prevBg = BankGaransi::where('customer_id', $targetBg->customer_id)
                            ->where('id', '<', $targetBg->id)
                            ->whereNotIn('status', ['draft', 'rejected', 'returned'])
                            ->orderBy('id', 'desc')
                            ->first();

                $remarks = null;
                $lampiranD = LampiranD::where('bg_submission_id', $sub->id)->with('activeVersion')->first();
                if ($lampiranD && $lampiranD->activeVersion) {
                    $remarks = $lampiranD->activeVersion->remarks;
                }

                // Catat History Perubahan
                BgHistory::create([
                    'bank_garansi_id'   => $targetBg->id,
                    'previous_nominal'  => $prevBg ? $prevBg->bg_nominal : 0,
                    'new_nominal'       => $targetBg->bg_nominal,
                    'previous_exp_date' => $prevBg ? $prevBg->exp_date : null,
                    'new_exp_date'      => $targetBg->exp_date,
                    'remarks'           => $remarks ?? 'Approved by Finance via Email Link',
                    'created_by'        => $approverId
                ]);
            }

            $pendingSiblings = BgSubmission::where('bg_recommendation_id', $sub->bg_recommendation_id)
                                ->where('status', '!=', 'completed')
                                ->where('status', '!=', 'approved')
                                ->count();

            if ($pendingSiblings == 0) {
                if ($sub->recommendation) {
                    $sub->recommendation->update(['status' => 'approved']);
                }

                $customerEmail = $sub->recommendation->customer->email;
                $salesEmails = User::role('head-SNM')->pluck('email')->toArray();
                $financeEmails = User::role(['manager-finance', 'head-finance'])->pluck('email')->toArray();

                $allRecipients = array_merge([$customerEmail], $salesEmails, $financeEmails);
                $recipients = array_unique(array_filter($allRecipients));

                foreach($recipients as $email) {
                    if(!empty($email)) {
                        Mail::to($email)->queue(new CustomerBgReadyMail($sub));
                    }
                }
            }
        }
    }
}
