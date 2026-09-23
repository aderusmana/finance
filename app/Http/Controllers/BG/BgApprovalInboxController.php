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

        $metadata = json_decode($rec->notes ?? '[]', true) ?? [];
        if ($sub->submission_type === 'adendum' || empty($rincianBank)) {
            if (!empty($metadata['bank_name'])) {
                $rincianBank = [
                    [
                        'bank_name' => $metadata['bank_name'] . (!empty($metadata['branch_name']) ? ' (' . $metadata['branch_name'] . ')' : ''),
                        'nominal'   => number_format($sub->bg_nominal, 0, ',', '.')
                    ]
                ];
                $totalNominal = $sub->bg_nominal;
            }
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

        $displayCreditLimit = $rec->credit_limit_updated;
        if ($displayCreditLimit <= 0 && $sub->bg_nominal > 0) {
            $rulePercent = $this->getLimitRulePercent($cust);
            $activeRule = $rulePercent > 0 ? $rulePercent : 100;
            $displayCreditLimit = $sub->bg_nominal / ($activeRule / 100);
        }

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
                'limit_kredit' => number_format($displayCreditLimit, 0, ',', '.'),
                'bg_ditetapkan' => number_format($rec->set_bg ?: $sub->bg_nominal, 0, ',', '.'),
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

        if (in_array($sub->status, ['completed', 'approved', 'rejected_by_finance'])) {
            $statusText = in_array($sub->status, ['completed', 'approved']) ? 'disetujui' : 'ditolak';
            return response()->json([
                'success' => false,
                'message' => "Pengajuan ini sudah diproses sebelumnya ({$statusText}) oleh Finance."
            ], 422);
        }

        DB::beginTransaction();
        try {
            $approverUser = auth()->user();
            $approverName = $approverUser->name ?? 'Finance';

            $status = ($request->action == 'reject') ? 'rejected_by_finance' : 'completed';
            $defaultNotes = ($request->action == 'reject')
                ? ($request->notes ?? 'Ditolak melalui Dashboard oleh ' . $approverName)
                : 'Disetujui melalui Dashboard oleh ' . $approverName;
            $newToken = ($status == 'completed') ? Str::random(60) : null;

            $sub->update([
                'status'        => $status,
                'token'         => $newToken,
                'reviewed_at'   => now(),
                'validated_by'  => auth()->id(),
                'validated_at'  => now(),
            ]);

            // 1. Update log approver yang sedang login atau log pending pertama
            $currentLog = ApprovalLog::where('related_id', $sub->id)
                ->where('category', 'BG')
                ->where('status', 'Pending')
                ->where('approver_nik', $approverUser->nik ?? null)
                ->first();

            if (!$currentLog) {
                $currentLog = ApprovalLog::where('related_id', $sub->id)
                    ->where('category', 'BG')
                    ->where('status', 'Pending')
                    ->first();
            }

            if ($currentLog) {
                $currentLog->update([
                    'status'        => ($request->action == 'reject') ? 'Rejected' : 'Approved',
                    'approver_name' => $approverName,
                    'notes'         => $defaultNotes,
                    'updated_at'    => now(),
                ]);
            }

            // 2. Kunci & update approval log pending lainnya untuk submission ini
            $siblingNote = ($request->action == 'reject')
                ? 'Dibatalkan karena telah ditolak melalui Dashboard oleh ' . $approverName . ($request->notes ? " (Alasan: {$request->notes})" : "")
                : 'Otomatis selesai karena telah disetujui melalui Dashboard oleh ' . $approverName;

            ApprovalLog::where('category', 'BG')
                ->where('related_id', $sub->id)
                ->when($currentLog, function ($q) use ($currentLog) {
                    $q->where('id', '!=', $currentLog->id);
                })
                ->where('status', 'Pending')
                ->update([
                    'status'     => ($request->action == 'reject') ? 'Rejected' : 'Approved',
                    'notes'      => $siblingNote,
                    'updated_at' => now(),
                ]);

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

                // Bersihkan draft BG yang dibuat untuk pengajuan ini jika ditolak
                BankGaransi::where('customer_id', $sub->recommendation->customer_id ?? 0)
                    ->where('status', 'draft')
                    ->whereBetween('created_at', [
                        $sub->created_at->copy()->subMinutes(5),
                        $sub->created_at->copy()->addMinutes(5)
                    ])
                    ->delete();
            }

            if ($status == 'completed') {
                if ($rec) {
                    $rec->update(['status' => 'approved']);
                }

                $isAdendum = ($sub->submission_type === 'adendum');
                $metadata = json_decode($rec->notes ?? '[]', true) ?? [];
                $targetBgId = $metadata['target_bg_id'] ?? null;

                // Fallback pencarian target BG adendum
                if (!$targetBgId && $isAdendum) {
                    $draftBg = BankGaransi::where('customer_id', $rec->customer_id)
                        ->where('is_adendum', 1)
                        ->whereNotNull('base_bg_id')
                        ->latest()
                        ->first();
                    if ($draftBg) {
                        $targetBgId = $draftBg->base_bg_id;
                    }
                }

                $targetBg = $targetBgId ? BankGaransi::find($targetBgId) : null;

                if ($isAdendum && $targetBg) {
                    // Meniban data Bank Garansi existing yang di-adendum
                    $oldNominal = $targetBg->bg_nominal;
                    $oldExpDate = $targetBg->exp_date;

                    $targetBg->update([
                        'bg_number'            => $sub->bg_number ?: $targetBg->bg_number,
                        'bg_nominal'           => $sub->bg_nominal ?: $targetBg->bg_nominal,
                        'exp_date'             => $sub->exp_date ?: $targetBg->exp_date,
                        'issued_date'          => now(),
                        'warkat_file_path'     => $sub->warkat_file_path ?: $targetBg->warkat_file_path,
                        'warkat_files'         => $sub->warkat_files ?: ($sub->warkat_file_path ? [$sub->warkat_file_path] : $targetBg->warkat_files),
                        'lampiran_d_file_path' => $sub->signed_document_path ?: $targetBg->lampiran_d_file_path,
                        'lampiran_d_files'     => $sub->signed_document_path ? [$sub->signed_document_path] : $targetBg->lampiran_d_files,
                        'is_adendum'           => 1,
                        'bg_type'              => 'new', // Selalu 'new'
                        'status'               => 'approved',
                    ]);

                    // Update detail bank
                    $bankName = $metadata['bank_name'] ?? null;
                    $branchName = $metadata['branch_name'] ?? '';
                    if ($bankName) {
                        $targetBg->details()->delete();
                        $targetBg->details()->create([
                            'bank_name'   => $bankName,
                            'branch_name' => $branchName,
                            'nominal'     => $targetBg->bg_nominal,
                        ]);
                    } else {
                        $targetBg->details()->update(['nominal' => $targetBg->bg_nominal]);
                    }

                    // Log history adendum
                    BgHistory::create([
                        'bank_garansi_id'   => $targetBg->id,
                        'previous_nominal'  => $oldNominal,
                        'new_nominal'       => $targetBg->bg_nominal,
                        'previous_exp_date' => $oldExpDate,
                        'new_exp_date'      => $targetBg->exp_date,
                        'remarks'           => "Adendum disetujui melalui Dashboard oleh {$approverName} (Form: {$sub->form_code})",
                        'created_by'        => auth()->id() ?? $sub->validated_by
                    ]);

                    // Hapus draft duplicate BG yang sempat dibuat saat sales submit adendum
                    BankGaransi::where('customer_id', $rec->customer_id)
                        ->where('id', '!=', $targetBg->id)
                        ->where(function($q) use ($targetBg, $sub) {
                            $q->where('base_bg_id', $targetBg->id)
                              ->orWhere('bg_number', $sub->bg_number);
                        })
                        ->where('status', 'draft')
                        ->delete();

                    $bgs = collect([$targetBg]);
                } else {
                    // Update Status SEMUA BG dalam batch ini (Tambah BG / Normal flow)
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
                            'bg_type'          => 'new', // Selalu 'new'
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
                }

                // Kalkulasi Credit Limit yang akurat berdasarkan inputan BG dan aturan limit
                $lampiranD = LampiranD::where('bg_submission_id', $sub->id)->with('activeVersion')->first();
                $creditLimitToApply = 0;

                if ($lampiranD && $lampiranD->activeVersion && !empty($lampiranD->activeVersion->data_snapshot['limit_kredit'])) {
                    $creditLimitToApply = (float) $lampiranD->activeVersion->data_snapshot['limit_kredit'];
                }

                if ($creditLimitToApply <= 0 && $sub->bg_nominal > 0) {
                    $rulePercent = $this->getLimitRulePercent($cust);
                    $activeRule = $rulePercent > 0 ? $rulePercent : 100;
                    $creditLimitToApply = (float) ($sub->bg_nominal / ($activeRule / 100));
                }

                if ($creditLimitToApply <= 0 && $rec && $rec->credit_limit_updated > 0) {
                    $creditLimitToApply = (float) $rec->credit_limit_updated;
                }

                // Background calculation & sync of Credit Limit to Customer
                if ($cust && $creditLimitToApply > 0) {
                    if ($rec) {
                        $rec->update([
                            'credit_limit_updated' => $creditLimitToApply,
                            'set_bg'               => $sub->bg_nominal ?: $rec->set_bg,
                        ]);
                    }

                    $cust->update([
                        'credit_limit'          => $creditLimitToApply,
                        'approved_credit_limit' => $creditLimitToApply,
                    ]);

                    CreditLimit::create([
                        'customer_id'           => $cust->id,
                        'bank_garansi_id'       => ($isAdendum && $targetBg) ? $targetBg->id : ($bgs->first()->id ?? null),
                        'recommendation_id'     => $rec ? $rec->id : null,
                        'requested_limit'       => $creditLimitToApply,
                        'approved_limit'        => $creditLimitToApply,
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
                            "Pembaruan Credit Limit untuk <b>{$custName}</b> sebesar <b>Rp " . number_format($creditLimitToApply, 0, ',', '.') . "</b> telah selesai diproses di background setelah validasi Bu Rita. Tidak perlu verifikasi/pengecekan lagi.",
                            route('customers.index'),
                            'ph-check-circle',
                            'info'
                        ));

                        $itEmails = $itUsers->pluck('email')->filter(fn($e) => !empty($e) && filter_var($e, FILTER_VALIDATE_EMAIL))->toArray();
                        $validatorName = $approverName;
                        foreach ($itEmails as $itEmail) {
                            Mail::to($itEmail)->queue(new CreditLimitUpdatedItMail($sub, $validatorName, $creditLimitToApply));
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
                    "Bank Guarantee & Lampiran D untuk <b>{$custName}</b> telah divalidasi oleh Finance ({$approverName}) dan Credit Limit otomatis diperbarui.",
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

    /**
     * Hitung persentase batas limit garansi bank customer dari tabel bg_limit_rules berdasarkan join_date
     */
    private function getLimitRulePercent($customer)
    {
        if (!$customer || !$customer->join_date) {
            return 0;
        }

        $joinDate = \Carbon\Carbon::parse($customer->join_date);
        $years    = (int) abs($joinDate->diffInYears(\Carbon\Carbon::now()));

        $rule = DB::table('bg_limit_rules')
            ->where('min_year', '<=', $years)
            ->where('max_year', '>=', $years)
            ->orderBy('min_year', 'desc')
            ->first();

        return $rule ? (float)$rule->percentage : 0;
    }
}
