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
use App\Notifications\SystemNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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

        $rec = $submission->recommendation;
        $metadata = json_decode($rec->notes, true) ?? [];
        $bg = null;

        if (isset($metadata['action']) && $metadata['action'] === 'existing' && !empty($metadata['target_bg_id'])) {
            $bg = BankGaransi::with('details')->find($metadata['target_bg_id']);
        }
        else {
            $createdAt = Carbon::parse($submission->created_at);
            $start = $createdAt->copy()->subMinutes(2);
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

        $sub = BgSubmission::with('recommendation.customer')->find($log->related_id);

        if (!$sub) {
            return abort(404, 'Data Submission tidak ditemukan');
        }

        $action = $request->action;
        $status = ($action == 'reject') ? 'Rejected' : 'Approved';

        DB::beginTransaction();
        try {
            $log->update([
                'status'     => $status,
                'notes'      => $request->notes,
                'updated_at' => now(),
                'token'      => null
            ]);

            $causer = auth()->user() ?? User::where('nik', $log->approver_nik)->first();
            $actionText = ($status == 'Rejected') ? 'Rejected Approval' : 'Approved Document';

            activity()
                ->causedBy($causer)
                ->performedOn($sub)
                ->useLog('approval_process')
                ->event($action)
                ->withProperties(['notes' => $request->notes, 'approver' => $log->approver_name])
                ->log("{$actionText} oleh Finance ({$log->approver_name})");

            if ($status == 'Rejected') {
                $sub->update(['status' => 'rejected_by_finance']);
            } else {
                $this->finalizeSubmission($log->related_id);
            }

            $admins = User::role(['super-admin'])->get();
            $statusBold = "<b>" . ($status == 'Approved' ? 'Disetujui' : 'Ditolak') . "</b>";
            $color = ($status == 'Approved') ? 'success' : 'danger';
            $icon  = ($status == 'Approved') ? 'ph-check-circle' : 'ph-x-circle';

            $custName = $sub->recommendation->customer->name ?? 'Unknown Customer';
            Notification::send($admins, new SystemNotification(
                "Submission {$statusBold}",
                "Pengajuan <b>{$custName}</b> telah {$statusBold} oleh Finance.",
                route('bg-submissions.index'),
                $icon,
                $color
            ));

            DB::commit();

            return view('page.customer_portal.form-success', [
                'type' => 'approval',
                'title' => 'Processed Successfully',
                'message' => 'Terima kasih, keputusan approval Anda telah disimpan.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Approval Error: " . $e->getMessage());
            return abort(500, 'Terjadi kesalahan sistem saat memproses approval.');
        }
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

            $rec = $sub->recommendation;
            $metadata = json_decode($rec->notes, true) ?? [];
            $targetBg = null;

            if (isset($metadata['action']) && $metadata['action'] === 'existing' && !empty($metadata['target_bg_id'])) {
                $targetBg = BankGaransi::find($metadata['target_bg_id']);
            }
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

            if ($targetBg) {
                $targetBg->update([
                    'status'      => 'approved',
                    'issued_date' => now(),
                    'exp_date'    => now()->addYear(),
                ]);

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
