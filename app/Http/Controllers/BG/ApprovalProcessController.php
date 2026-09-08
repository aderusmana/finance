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
use App\Models\Customer\CreditLimit;
use App\Mail\CreditLimitUpdatedItMail;
use App\Notifications\SystemNotification;
use App\Models\BG\BgRecommendation;
use App\Mail\CustomerFillFormNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        if ($log->sub_category === 'Sales Recommendation') {
            if ($action === 'approve') {
                $log->update([
                    'status' => 'Approved',
                    'updated_at' => now(),
                    'token' => null
                ]);

                $this->finalizeSalesRecommendationApproval($log->related_id, $log->approver_nik);

                return view('page.customer_portal.form-success', [
                    'type' => 'approval',
                    'title' => 'Rekomendasi Berhasil Disetujui',
                    'message' => 'Terima kasih, rekomendasi Bank Garansi telah disetujui. Tautan portal formulir pendaftaran telah otomatis dikirimkan ke distributor.'
                ]);
            }
            abort(404);
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

        if ($log->sub_category === 'Sales Recommendation') {
            $recommendation = BgRecommendation::with(['customer', 'periods', 'tax'])->findOrFail($log->related_id);
            return view('page.customer_portal.sales_recommendation_approval', compact('log', 'recommendation'));
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
             return abort(404, 'Bank Guarantee data not found. Possible Timestamp mismatch or incorrect ID.');
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

        if ($log->sub_category === 'Sales Recommendation') {
            $rec = BgRecommendation::with('customer')->findOrFail($log->related_id);
            $action = $request->input('action', 'approve');
            $ronalUser = User::where('nik', $log->approver_nik)->first() ?? auth()->user();

            if ($action === 'reject') {
                $request->validate([
                    'notes' => 'required|string|min:3'
                ], [
                    'notes.required' => 'Mohon isi catatan atau alasan penolakan.'
                ]);

                $log->update([
                    'status' => 'Rejected',
                    'notes' => $request->notes,
                    'updated_at' => now(),
                    'token' => null
                ]);

                $rec->update([
                    'status' => 'rejected_by_sales',
                    'rejection_reason' => $request->notes,
                    'token' => null
                ]);

                activity()
                    ->causedBy($ronalUser)
                    ->performedOn($rec)
                    ->useLog('bg_recommendation')
                    ->event('sales_reject')
                    ->withProperties(['customer' => $rec->customer->name ?? '-', 'reason' => $request->notes])
                    ->log("Sales (Pak Ronal) rejected BG Recommendation via email/form. Reason: {$request->notes}");

                $admins = User::role(['admin-rtm', 'super-admin'])->get();
                Notification::sendNow($admins, new SystemNotification(
                    'BG Recommendation Rejected by Sales',
                    "Rekomendasi untuk <b>{$rec->customer->name}</b> ditolak oleh Pak Ronal. Alasan: <i>\"{$request->notes}\"</i>",
                    route('bg-recommendations.index'),
                    'ph-x-circle',
                    'danger'
                ));

                return view('page.customer_portal.form-success', [
                    'type' => 'approval',
                    'title' => 'Rekomendasi Ditolak',
                    'message' => 'Status rekomendasi telah diperbarui menjadi rejected. Admin-RTM telah menerima notifikasi untuk melakukan revisi/resubmit.'
                ]);
            } else {
                $log->update([
                    'status' => 'Approved',
                    'notes' => $request->notes,
                    'updated_at' => now(),
                    'token' => null
                ]);

                $this->finalizeSalesRecommendationApproval($log->related_id, $log->approver_nik);

                return view('page.customer_portal.form-success', [
                    'type' => 'approval',
                    'title' => 'Rekomendasi Berhasil Disetujui',
                    'message' => 'Terima kasih, rekomendasi Bank Garansi telah disetujui. Tautan portal pengisian formulir telah otomatis dikirimkan ke distributor.'
                ]);
            }
        }

        $sub = BgSubmission::with('recommendation.customer')->find($log->related_id);

        if (!$sub) {
            return abort(404, 'Submission data not found');
        }

        $action = $request->action;
        $status = ($action == 'reject') ? 'Rejected' : 'Approved';

        if ($status == 'Rejected') {
            $request->validate([
                'notes' => 'required|string|min:3'
            ], [
                'notes.required' => 'Alasan penolakan / revisi wajib diisi.'
            ]);
        }

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
                ->log("{$actionText} by Finance ({$log->approver_name})");

            $custName = $sub->recommendation->customer->name ?? 'Unknown Customer';

            if ($status == 'Rejected') {
                $sub->update(['status' => 'rejected_by_finance']);

                $recipients = User::role(['admin-rtm', 'super-admin'])->get();
                $reasonText = $request->notes ? " Alasan: <i>\"{$request->notes}\"</i>. Silakan perbaiki dan submit kembali." : "";
                Notification::send($recipients, new SystemNotification(
                    "Lampiran D Perlu Revisi",
                    "Perubahan Lampiran D untuk <b>{$custName}</b> ditolak oleh Manager Finance.{$reasonText}",
                    route('lampiran-d.index'),
                    'ph-x-circle',
                    'danger'
                ));
            } else {
                $this->finalizeSubmission($log->related_id);

                $recipients = User::role(['admin-rtm', 'secretary-finance', 'super-admin'])->get();
                Notification::send($recipients, new SystemNotification(
                    "Lampiran D Disetujui",
                    "Perubahan Lampiran D pada <b>{$custName}</b> telah di-approved oleh Manager Finance dan siap di-download atau digunakan.",
                    route('lampiran-d.index'),
                    'ph-check-circle',
                    'success'
                ));
            }

            DB::commit();

            return view('page.customer_portal.form-success', [
                'type' => 'approval',
                'title' => 'Processed Successfully',
                'message' => 'Thank you, your approval decision has been saved.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Approval Error: " . $e->getMessage());
            return abort(500, 'A system error occurred while processing the approval.');
        }
    }

    private function finalizeSubmission($submissionId, $approverId = null) {
        $sub = BgSubmission::with(['recommendation.customer'])->find($submissionId);

        if($sub) {
            $secretaryUser = User::role('secretary-finance')->first();
            $approverUserId = $approverId ?? ($secretaryUser ? $secretaryUser->id : null);

            $sub->update([
                'status'       => 'completed',
                'token'        => Str::random(60),
                'reviewed_at'  => now(),
                'validated_by' => $approverUserId,
                'validated_at' => now(),
            ]);

            $rec = $sub->recommendation;
            $cust = $rec ? $rec->customer : null;
            $metadata = json_decode($rec->notes ?? '[]', true) ?? [];
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
                    'status'           => 'approved',
                    'issued_date'      => now(),
                    'exp_date'         => $sub->exp_date ?? $targetBg->exp_date ?? now()->addYear(),
                    'bg_number'        => $sub->bg_number ?? $targetBg->bg_number,
                    'warkat_file_path' => $sub->warkat_file_path ?? $targetBg->warkat_file_path,
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
                    'remarks'           => $remarks ?? 'Approved by Secretary Finance via Email Link',
                    'created_by'        => $approverUserId
                ]);
            }

            // Background update credit limit & sync to customer
            if ($cust && $rec) {
                $cust->update([
                    'credit_limit'          => $rec->credit_limit_updated,
                    'approved_credit_limit' => $rec->credit_limit_updated,
                ]);

                $lampiranD = LampiranD::where('bg_submission_id', $sub->id)->first();
                CreditLimit::create([
                    'customer_id'           => $cust->id,
                    'bank_garansi_id'       => $targetBg ? $targetBg->id : null,
                    'recommendation_id'     => $rec->id,
                    'requested_limit'       => $rec->credit_limit_updated,
                    'approved_limit'        => $rec->credit_limit_updated,
                    'lampiran_d_version_id' => $lampiranD ? $lampiranD->active_version_id : null,
                    'approved_by'           => $approverUserId,
                    'approved_at'           => now(),
                ]);

                // Inform IT team (Info only, no action needed)
                try {
                    $itUsers = User::role('it')->get();
                    if ($itUsers->isNotEmpty()) {
                        Notification::send($itUsers, new SystemNotification(
                            "Info IT: Background Credit Limit Sync Selesai",
                            "Pembaruan Credit Limit untuk <b>{$cust->name}</b> sebesar <b>Rp " . number_format($rec->credit_limit_updated, 0, ',', '.') . "</b> telah selesai diproses di background via email approval Bu Rita. Tidak perlu cek manual.",
                            route('customers.index'),
                            'ph-check-circle',
                            'info'
                        ));

                        $itEmails = $itUsers->pluck('email')->filter(fn($e) => !empty($e) && filter_var($e, FILTER_VALIDATE_EMAIL))->toArray();
                        foreach ($itEmails as $itEmail) {
                            Mail::to($itEmail)->queue(new CreditLimitUpdatedItMail($sub, 'Secretary Finance (Bu Rita)'));
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Gagal notifikasi IT via ApprovalProcessController: " . $e->getMessage());
                }
            }

            $pendingSiblings = BgSubmission::where('bg_recommendation_id', $sub->bg_recommendation_id)
                                ->where('status', '!=', 'completed')
                                ->where('status', '!=', 'approved')
                                ->count();

            if ($pendingSiblings == 0) {
                if ($sub->recommendation) {
                    $sub->recommendation->update(['status' => 'approved']);
                }

                // STRICT: Lampiran D dikirim HANYA ke admin-rtm dan manager purchasing
                $adminRtmEmails = User::role('admin-rtm')->pluck('email')->toArray();
                $purchasingEmail = ($cust && !empty($cust->purchasing_manager_email)) ? [$cust->purchasing_manager_email] : [];

                $targetEmails = array_unique(array_filter(
                    array_merge($adminRtmEmails, $purchasingEmail),
                    fn($e) => !empty($e) && filter_var($e, FILTER_VALIDATE_EMAIL)
                ));

                foreach($targetEmails as $email) {
                    Mail::to($email)->queue(new CustomerBgReadyMail($sub));
                }
            }
        }
    }

    private function finalizeSalesRecommendationApproval($recId, $approverNik = null)
    {
        $rec = BgRecommendation::with(['customer', 'periods', 'tax'])->findOrFail($recId);
        $user = $approverNik ? User::where('nik', $approverNik)->first() : auth()->user();

        $token = Str::random(64);
        $rec->update([
            'status'            => 'process',
            'token'             => $token,
            'sales_approved_by' => $user->id ?? 11,
            'sales_approved_at' => now(),
        ]);

        activity()
            ->causedBy($user)
            ->performedOn($rec)
            ->useLog('bg_recommendation')
            ->event('sales_approve')
            ->withProperties([
                'customer' => $rec->customer->name ?? '-',
                'set_bg'   => $rec->set_bg,
                'credit_limit' => $rec->credit_limit_updated
            ])
            ->log("Sales (Pak Ronal) approved BG Recommendation via token. Token generated and link sent to customer.");

        // Send email to customer with portal link
        $custEmail = $rec->customer->email ?? null;
        if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($custEmail)->send(new CustomerFillFormNotification($rec));
            } catch (\Exception $e) {
                Log::error('CustomerFillFormNotification Mail Error: ' . $e->getMessage());
            }
        }

        // Notify Admin-RTM
        try {
            $admins = User::role(['admin-rtm', 'super-admin'])->get();
            Notification::sendNow($admins, new SystemNotification(
                'BG Recommendation Approved by Sales',
                "Rekomendasi untuk <b>{$rec->customer->name}</b> telah disetujui oleh Pak Ronal dan dikirim ke customer.",
                route('bg-recommendations.index'),
                'ph-check-circle',
                'success'
            ));
        } catch (\Exception $notifEx) {
            Log::error('Notif Admin Error: ' . $notifEx->getMessage());
        }
    }
}
