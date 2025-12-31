<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgHistory;
use App\Models\BG\LampiranD;
use App\Models\Master\ApprovalLog;
use App\Jobs\ProcessFinanceApprovalEmail;
use App\Mail\CustomerBgReadyMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class BgApprovalInboxController extends Controller
{
    /**
     * Menampilkan Halaman List Approval (Inbox)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil Submission yang statusnya 'waiting_approval'
            $query = BgSubmission::with(['recommendation.customer'])
                        ->where('status', 'waiting_approval')
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
                    // LOGIC BARU: Ambil BG terbaru berdasarkan Customer ID (Tanpa filter status ketat)
                    $bg = BankGaransi::where('customer_id', $row->recommendation->customer_id)
                            ->latest() // Ambil yang paling baru dibuat
                            ->first();

                    return $bg ? 'Rp ' . number_format($bg->bg_nominal, 0, ',', '.') : '-';
                })
                ->addColumn('submitted_at', function($row){
                    return $row->updated_at->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-success btn-quick-approve" data-id="'.$row->id.'" title="Quick Approve">
                                <i class="ph-bold ph-check"></i>
                            </button>
                            <button class="btn btn-sm btn-warning btn-review text-white" data-id="'.$row->id.'" title="Review with Notes">
                                <i class="ph-bold ph-pencil-simple"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-reject" data-id="'.$row->id.'" title="Reject">
                                <i class="ph-bold ph-x"></i>
                            </button>
                            <button class="btn btn-sm btn-info btn-resend text-white" data-id="'.$row->id.'" title="Resend Email Notif">
                                <i class="ph-bold ph-envelope-simple"></i>
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
     * Mengambil Data Lampiran D KOMPLIT untuk Modal (Ajax)
     */
    public function getModalData($id)
    {
        $sub = BgSubmission::with(['recommendation.customer', 'lampiranD'])->findOrFail($id);
        $rec = $sub->recommendation;
        $cust = $rec->customer;

        // Ambil BG Terbaru
        $bg = BankGaransi::where('customer_id', $cust->id)
                ->latest()
                ->first();

        // Cek periode
        $periodeStr = '-';
        if ($rec->periods && $rec->periods->count() > 0) {
            $start = $rec->periods->min('period_date');
            $end   = $rec->periods->max('period_date');
            $periodeStr = \Carbon\Carbon::parse($start)->translatedFormat('F Y') . ' - ' .
                          \Carbon\Carbon::parse($end)->translatedFormat('F Y');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nama_distributor' => $cust->name,
                'kota' => $cust->city,
                'wilayah' => $cust->area ?? '-',
                'periode' => $periodeStr,
                'avg_sales' => number_format($rec->average, 0, ',', '.'),
                'top' => $rec->top,
                'lead_time' => $rec->lead_time,
                'inflasi' => $rec->inflation,
                'limit_kredit' => number_format($rec->credit_limit_updated, 0, ',', '.'),
                'bg_ditetapkan' => number_format($rec->set_bg, 0, ',', '.'),
                'bg_diserahkan' => number_format($bg->bg_nominal ?? 0, 0, ',', '.'),

                // Extra Info
                'form_code' => $sub->form_code,
            ]
        ]);
    }

    /**
     * Proses Utama: Approve / Reject / Quick Approve
     */
    public function process(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'action' => 'required|in:approve,reject',
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
            $notes  = $request->notes ?? 'Processed via Dashboard (Quick Action)';

            $sub->update([  
                'status' => $status,
                'reviewed_at' => now()
            ]);

            if ($log) {
                $log->update([
                    'status' => ($request->action == 'reject') ? 'Rejected' : 'Approved',
                    'notes' => $notes,
                    'updated_at' => now(),
                    'token' => null
                ]);
            }

            if ($status == 'completed') {
                if ($sub->recommendation) {
                    $sub->recommendation->update(['status' => 'approved']);
                }

                $bg = BankGaransi::where('customer_id', $sub->recommendation->customer_id)
                        ->where('status', 'submitted')
                        ->latest()
                        ->first();

                if ($bg) {
                    $bg->update([
                        'status'      => 'approved',
                        'issued_date' => now(),
                        'exp_date'    => now()->addYear(),
                    ]);
                }

                $this->addToHistoryLogic($sub);
                $this->sendCompletionEmails($sub);
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

        if ($log) {
            ProcessFinanceApprovalEmail::dispatch($log, $sub);
            return response()->json(['success' => true, 'message' => 'Email notification resent to Approver.']);
        }
        return response()->json(['success' => false, 'message' => 'No pending approval log found.']);
    }

    // --- PRIVATE METHODS ---

    private function addToHistoryLogic($submission)
    {
        $currentBg = BankGaransi::where('customer_id', $submission->recommendation->customer_id)
                    ->latest() // Ambil yang paling baru
                    ->first();

        if (!$currentBg) return;

        $prevBg = BankGaransi::where('customer_id', $currentBg->customer_id)
                    ->where('id', '<', $currentBg->id)
                    ->orderBy('id', 'desc')
                    ->first();

        $remarks = 'Approved via Dashboard';
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

    private function sendCompletionEmails($submission)
    {
        $customerEmail = $submission->recommendation->customer->email;
        $salesEmails = User::role('head-SNM')->pluck('email')->toArray();
        $financeEmails = User::role('manager-finance')->pluck('email')->toArray();

        $allRecipients = array_merge([$customerEmail], $salesEmails, $financeEmails);
        $recipients = array_unique(array_filter($allRecipients));

        foreach($recipients as $email) {
            if (!empty($email)) {
                Mail::to($email)->queue(new CustomerBgReadyMail($submission));
            }
        }
    }
}
