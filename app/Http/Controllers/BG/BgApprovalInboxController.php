<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgHistory;
use App\Models\BG\LampiranD;
use App\Models\Customer\CreditLimit;
use App\Models\Master\ApprovalLog;
use App\Jobs\ProcessFinanceApprovalEmail;
use App\Mail\CustomerBgReadyMail;
use App\Mail\CreditLimitUpdatedItMail;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BgApprovalInboxController extends Controller
{
    /**
     * Menampilkan Halaman List Approval (Inbox)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil Submission yang statusnya 'waiting_approval' atau 'uploaded'
            $query = BgSubmission::with(['recommendation.customer'])
                        ->whereIn('status', ['waiting_approval', 'uploaded'])
                        ->orderBy('updated_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function($row){
                    return $row->recommendation->customer->name ?? '-';
                })
                ->addColumn('form_code', function($row){
                    return '<span class="fw-bold text-primary">'.$row->form_code.'</span>';
                })
                ->addColumn('bg_nominal', function($row){
                    $total = BankGaransi::where('customer_id', $row->recommendation->customer_id)
                            ->whereBetween('created_at', [
                                $row->created_at->copy()->subMinutes(5),
                                $row->created_at->copy()->addMinutes(5)
                            ])
                            ->sum('bg_nominal');

                    if ($total == 0 && $row->bg_nominal > 0) {
                        $total = $row->bg_nominal;
                    }

                    return 'Rp ' . number_format($total, 0, ',', '.');
                })
                ->addColumn('submitted_at', function($row){
                    return $row->updated_at->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="action-btn-group">
                            <button class="btn btn-primary action-btn-hover btn-review" data-id="'.$row->id.'" data-tooltip="Review Submission">
                                <i class="ph-bold ph-eye text-white"></i> Review
                            </button>
                            <button class="btn btn-warning action-btn-hover btn-resend" data-id="'.$row->id.'" data-tooltip="Resend Email Notif">
                                <i class="ph-bold ph-envelope-simple text-white"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['form_code', 'action'])
                ->make(true);
        }

        return view('page.bg.bg_approvals.index');
    }

    /**
     * Mengambil Data Lampiran D & Warkat KOMPLIT untuk Modal (Ajax)
     */
    public function getModalData($id)
    {
        $sub = BgSubmission::with(['recommendation.customer', 'lampiranD'])->findOrFail($id);
        $rec = $sub->recommendation;
        $cust = $rec->customer;

        $bgs = BankGaransi::where('customer_id', $cust->id)
                ->whereBetween('created_at', [
                    $sub->created_at->copy()->subMinutes(5),
                    $sub->created_at->copy()->addMinutes(5)
                ])
                ->with('details')
                ->get();

        if ($bgs->isEmpty()) {
            $bgs = BankGaransi::where('customer_id', $cust->id)->latest()->take(3)->with('details')->get();
        }

        $totalNominal = $bgs->sum('bg_nominal');
        if ($totalNominal == 0 && $sub->bg_nominal > 0) {
            $totalNominal = $sub->bg_nominal;
        }

        $rincianBank = [];
        foreach ($bgs as $bgItem) {
            $detail = $bgItem->details->first();
            $rincianBank[] = [
                'bank_name' => $detail ? $detail->bank_name : ($bgItem->bank_name ?? 'Bank'),
                'nominal'   => number_format($bgItem->bg_nominal, 0, ',', '.')
            ];
        }

        $periodeStr = '-';
        if ($rec->periods && $rec->periods->count() > 0) {
            $start = $rec->periods->min('period_date');
            $end   = $rec->periods->max('period_date');
            $periodeStr = \Carbon\Carbon::parse($start)->translatedFormat('F Y') . ' - ' .
                          \Carbon\Carbon::parse($end)->translatedFormat('F Y');
        }

        $firstBg = $bgs->first();
        $bgNumber = $sub->bg_number ?? ($firstBg->bg_number ?? '-');
        $expDate = $sub->exp_date ? \Carbon\Carbon::parse($sub->exp_date)->format('d M Y') : ($firstBg && $firstBg->exp_date ? \Carbon\Carbon::parse($firstBg->exp_date)->format('d M Y') : '-');
        $warkatUrl = $sub->warkat_file_path ? asset($sub->warkat_file_path) : ($firstBg && $firstBg->warkat_file_path ? asset($firstBg->warkat_file_path) : null);
        $signedDocUrl = $sub->signed_document_path ? asset($sub->signed_document_path) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'nama_distributor' => $cust->name,
                'kota' => $cust->city,
                'wilayah' => $cust->area ?? '-',
                'custom_address' => $sub->custom_address ?? $cust->address1,
                'periode' => $periodeStr,
                'avg_sales' => number_format($rec->average, 0, ',', '.'),
                'top' => $rec->top,
                'lead_time' => $rec->lead_time,
                'inflasi' => $rec->inflation,
                'limit_kredit' => number_format($rec->credit_limit_updated, 0, ',', '.'),
                'bg_ditetapkan' => number_format($rec->set_bg, 0, ',', '.'),
                'bg_diserahkan_total' => number_format($totalNominal, 0, ',', '.'),
                'rincian_bank' => $rincianBank,
                'form_code' => $sub->form_code,
                'bg_number' => $bgNumber,
                'exp_date' => $expDate,
                'warkat_url' => $warkatUrl,
                'signed_doc_url' => $signedDocUrl,
            ]
        ]);
    }

    /**
     * Proses Utama: Approve / Reject
     */
    public function process(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'action' => 'required|in:approve,reject',
            'notes' => 'required_if:action,reject|nullable|string|min:3',
        ], [
            'notes.required_if' => 'Alasan penolakan / revisi wajib diisi.'
        ]);

        $sub = BgSubmission::with('recommendation.customer')->findOrFail($request->id);

        $log = ApprovalLog::where('related_id', $sub->id)
                ->where('category', 'BG')
                ->where('status', 'Pending')
                ->latest()
                ->first();

        DB::beginTransaction();
        try {
            $status = ($request->action == 'reject') ? 'rejected_by_finance' : 'completed';
            $notes  = $request->notes ?? 'Processed via Dashboard by Secretary Finance';
            $newToken = ($status == 'completed') ? Str::random(60) : null;

            $sub->update([
                'status'        => $status,
                'token'         => $newToken,
                'reviewed_at'   => now(),
                'validated_by'  => auth()->id(),
                'validated_at'  => now(),
            ]);

            if ($log) {
                $log->update([
                    'status'     => ($request->action == 'reject') ? 'Rejected' : 'Approved',
                    'notes'      => $notes,
                    'updated_at' => now(),
                    'token'      => null
                ]);
            }

            $cust = $sub->recommendation->customer ?? null;
            $custName = $cust ? $cust->name : 'Unknown Customer';
            $rec = $sub->recommendation;

            if ($status == 'rejected_by_finance') {
                $recipients = User::role(['admin-rtm', 'super-admin'])->get();
                $reasonText = $notes ? " Alasan: <i>\"{$notes}\"</i>. Silakan perbaiki dan submit kembali." : "";
                Notification::send($recipients, new SystemNotification(
                    "Lampiran D Perlu Revisi",
                    "Perubahan Lampiran D untuk <b>{$custName}</b> ditolak oleh Finance.{$reasonText}",
                    route('lampiran-d.index'),
                    'ph-x-circle',
                    'danger'
                ));
            }

            if ($status == 'completed') {
                if ($rec) {
                    $rec->update(['status' => 'approved']);
                }

                // Update Status SEMUA BG dalam batch ini
                $bgs = BankGaransi::where('customer_id', $rec->customer_id)
                        ->whereBetween('created_at', [
                            $sub->created_at->copy()->subMinutes(5),
                            $sub->created_at->copy()->addMinutes(5)
                        ])
                        ->get();

                if ($bgs->isEmpty()) {
                    $bgs = BankGaransi::where('customer_id', $rec->customer_id)->latest()->take(3)->get();
                }

                foreach($bgs as $bg) {
                    $bgUpdateData = [
                        'status'           => 'approved',
                        'issued_date'      => now(),
                        'exp_date'         => $sub->exp_date ?? $bg->exp_date ?? now()->addYear(),
                        'warkat_file_path' => $sub->warkat_file_path ?? $bg->warkat_file_path,
                    ];
                    if ($bgs->count() === 1 && $sub->bg_number) {
                        $bgUpdateData['bg_number'] = $sub->bg_number;
                    }
                    $bg->update($bgUpdateData);
                    $this->addToHistoryLogic($sub, $bg);
                }

                // Background calculation & sync of Credit Limit to Customer
                if ($cust && $rec) {
                    $cust->update([
                        'credit_limit'          => $rec->credit_limit_updated,
                        'approved_credit_limit' => $rec->credit_limit_updated,
                    ]);

                    $lampiranD = LampiranD::where('bg_submission_id', $sub->id)->first();
                    CreditLimit::create([
                        'customer_id'           => $cust->id,
                        'bank_garansi_id'       => $bgs->first()->id ?? null,
                        'recommendation_id'     => $rec->id,
                        'requested_limit'       => $rec->credit_limit_updated,
                        'approved_limit'        => $rec->credit_limit_updated,
                        'lampiran_d_version_id' => $lampiranD ? $lampiranD->active_version_id : null,
                        'approved_by'           => auth()->id(),
                        'approved_at'           => now(),
                    ]);
                }

                // Inform IT team (Informational only, background calculation completed)
                try {
                    $itUsers = User::role('it')->get();
                    if ($itUsers->isNotEmpty()) {
                        Notification::send($itUsers, new SystemNotification(
                            "Info IT: Background Credit Limit Sync Selesai",
                            "Pembaruan Credit Limit untuk <b>{$custName}</b> sebesar <b>Rp " . number_format($rec->credit_limit_updated, 0, ',', '.') . "</b> telah selesai diproses di background setelah validasi Bu Rita. Tidak perlu verifikasi/pengecekan lagi.",
                            route('customers.index'),
                            'ph-check-circle',
                            'info'
                        ));

                        $itEmails = $itUsers->pluck('email')->filter(fn($e) => !empty($e) && filter_var($e, FILTER_VALIDATE_EMAIL))->toArray();
                        $validatorName = auth()->user()->name ?? 'Secretary Finance (Bu Rita)';
                        foreach ($itEmails as $itEmail) {
                            Mail::to($itEmail)->queue(new CreditLimitUpdatedItMail($sub, $validatorName));
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Gagal notifikasi IT: " . $e->getMessage());
                }

                // Send Lampiran D strictly to admin-rtm and purchasing_manager_email only
                $this->sendCompletionEmails($sub);

                $recipients = User::role(['admin-rtm', 'secretary-finance', 'super-admin'])->get();
                Notification::send($recipients, new SystemNotification(
                    "Lampiran D & BG Validated",
                    "Bank Guarantee & Lampiran D untuk <b>{$custName}</b> telah divalidasi oleh Secretary Finance (Bu Rita) dan Credit Limit otomatis diperbarui.",
                    route('lampiran-d.index'),
                    'ph-check-circle',
                    'success'
                ));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Submission has been ' . $request->action . 'd successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function resendEmail($id)
    {
        $sub = BgSubmission::findOrFail($id);
        $log = ApprovalLog::where('related_id', $sub->id)
                ->where('category', 'BG')
                ->where('status', 'Pending')
                ->first();

        $approverUser = User::where('email', $log->approver_email)->first();

        if($approverUser) {
             Notification::send($approverUser, new SystemNotification(
                'Reminder Approval',
                "Hello, please review the submission <b>{$log->description}</b> immediately.",
                route('bg-approvals.index'),
                'ph-bell-ringing',
                'danger'
            ));
        }

        if ($log) {
            ProcessFinanceApprovalEmail::dispatch($log, $sub);
            return response()->json(['success' => true, 'message' => 'Email notification resent to Approver.']);
        }
        return response()->json(['success' => false, 'message' => 'No pending approval log found.']);
    }

    // --- PRIVATE METHODS ---

    private function addToHistoryLogic($submission, $currentBg)
    {
        if (!$currentBg) return;

        $prevBg = BankGaransi::where('customer_id', $currentBg->customer_id)
                    ->where('id', '<', $currentBg->id)
                    ->orderBy('id', 'desc')
                    ->first();

        $remarks = 'Approved by Secretary Finance';
        $lampiranD = LampiranD::where('bg_submission_id', $submission->id)->with('activeVersion')->first();
        if ($lampiranD && $lampiranD->activeVersion) {
            $remarks = $lampiranD->activeVersion->remarks;
        }

        BgHistory::create([
            'bank_garansi_id'   => $currentBg->id,
            'previous_nominal'  => $prevBg ? $prevBg->bg_nominal : 0,
            'new_nominal'       => $currentBg->bg_nominal,
            'previous_exp_date' => $prevBg ? $prevBg->exp_date : null,
            'new_exp_date'      => $currentBg->exp_date,
            'remarks'           => $remarks,
            'created_by'        => auth()->id()
        ]);
    }

    /**
     * Send Lampiran D strictly to admin-rtm and manager purchasing only
     */
    private function sendCompletionEmails($submission)
    {
        $pendingSiblings = BgSubmission::where('bg_recommendation_id', $submission->bg_recommendation_id)
                            ->where('status', '!=', 'completed')
                            ->where('status', '!=', 'approved')
                            ->count();

        if ($pendingSiblings > 0) {
            return;
        }

        $rec = $submission->recommendation;
        $cust = $rec ? $rec->customer : null;

        // Lampiran D dikirim HANYA ke admin-rtm dan manager purchasing
        $adminRtmEmails = User::role('admin-rtm')->pluck('email')->toArray();
        $purchasingEmail = ($cust && !empty($cust->purchasing_manager_email)) ? [$cust->purchasing_manager_email] : [];

        $targetEmails = array_unique(array_filter(
            array_merge($adminRtmEmails, $purchasingEmail),
            fn($e) => !empty($e) && filter_var($e, FILTER_VALIDATE_EMAIL)
        ));

        foreach($targetEmails as $email) {
            Mail::to($email)->queue(new CustomerBgReadyMail($submission));
        }
    }
}
