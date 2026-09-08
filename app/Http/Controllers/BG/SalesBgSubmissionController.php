<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BgSubmission;
use App\Models\Customer\Customer;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Master\ApprovalLog;
use App\Jobs\ProcessFinanceApprovalEmail;
use Yajra\DataTables\Facades\DataTables;

class SalesBgSubmissionController extends Controller
{
    /**
     * Display listing of Sales Submissions (Adendum & Tambah BG)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BgSubmission::with(['recommendation.customer'])
                        ->whereIn('submission_type', ['adendum', 'tambah_bg'])
                        ->orderBy('id', 'desc');

            if ($request->has('submission_type') && $request->submission_type !== 'all') {
                $query->where('submission_type', $request->submission_type);
            }
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('form_code', function($row){
                    return '<span class="fw-bold text-primary font-monospace">'.$row->form_code.'</span>';
                })
                ->addColumn('customer_name', function($row){
                    return $row->recommendation->customer->name ?? '-';
                })
                ->addColumn('submission_type', function($row){
                    if ($row->submission_type === 'adendum') {
                        return '<span class="badge bg-warning text-dark"><i class="ph-bold ph-pencil-simple me-1"></i> Adendum BG</span>';
                    }
                    return '<span class="badge bg-info text-white"><i class="ph-bold ph-plus-circle me-1"></i> Tambah BG</span>';
                })
                ->addColumn('bg_number', function($row){
                    return '<span class="font-monospace fw-semibold">'.($row->bg_number ?? '-').'</span>';
                })
                ->addColumn('bg_nominal', function($row){
                    return 'Rp ' . number_format($row->bg_nominal ?? 0, 0, ',', '.');
                })
                ->addColumn('exp_date', function($row){
                    return $row->exp_date ? \Carbon\Carbon::parse($row->exp_date)->format('d M Y') : '-';
                })
                ->addColumn('status', function($row){
                    $color = 'secondary';
                    $label = $row->status;

                    if ($row->status === 'waiting_approval' || $row->status === 'uploaded') {
                        $color = 'warning';
                        $label = 'Waiting Approval (Bu Rita)';
                    } elseif ($row->status === 'completed' || $row->status === 'approved') {
                        $color = 'success';
                        $label = 'Approved';
                    } elseif (str_contains($row->status, 'reject')) {
                        $color = 'danger';
                        $label = 'Rejected';
                    }

                    return '<span class="badge bg-'.$color.' status-badge-lg">'.$label.'</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="action-btn-group">';
                    if ($row->warkat_file_path) {
                        $btn .= '<a href="'.asset($row->warkat_file_path).'" target="_blank" class="btn btn-info action-btn-hover text-white" data-tooltip="Lihat Bank Garansi"><i class="ph-bold ph-file-text text-white"></i></a>';
                    }
                    if ($row->signed_document_path) {
                        $btn .= '<a href="'.asset($row->signed_document_path).'" target="_blank" class="btn btn-success action-btn-hover text-white" data-tooltip="Lihat Dokumen"><i class="ph-bold ph-file-pdf text-white"></i></a>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['form_code', 'submission_type', 'bg_number', 'status', 'action'])
                ->make(true);
        }

        $stats = [
            'total'   => BgSubmission::whereIn('submission_type', ['adendum', 'tambah_bg'])->count(),
            'adendum' => BgSubmission::where('submission_type', 'adendum')->count(),
            'tambah'  => BgSubmission::where('submission_type', 'tambah_bg')->count(),
            'pending' => BgSubmission::whereIn('submission_type', ['adendum', 'tambah_bg'])->whereIn('status', ['waiting_approval', 'uploaded'])->count(),
        ];

        $customers = Customer::where('status', 'active')
                        ->orderBy('name')
                        ->get(['id', 'name', 'code', 'no_pkd']);

        return view('page.bg.sales_submissions.index', compact('stats', 'customers'));
    }

    /**
     * Show form for Sales to submit Adendum or Tambah BG (Redirect to index modal)
     */
    public function create()
    {
        return redirect()->route('sales-submissions.index', ['action' => 'create']);
    }

    /**
     * Fetch active BGs for a specific customer (for Adendum selection)
     */
    public function getCustomerBgs($customerId)
    {
        $bgs = BankGaransi::where('customer_id', $customerId)
                    ->where('status', 'approved')
                    ->with('details')
                    ->get();

        return response()->json([
            'success' => true,
            'bgs' => $bgs
        ]);
    }

    /**
     * Store new Sales Submission (Direct Adendum / Tambah BG)
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'submission_type'   => 'required|in:adendum,tambah_bg',
            'existing_bg_id'    => 'required_if:submission_type,adendum|nullable|exists:bank_garansi,id',
            'bank_name'         => 'required|string|max:100',
            'branch_name'       => 'nullable|string|max:100',
            'bg_number'         => 'required|string|max:100',
            'bg_nominal'        => 'required',
            'issued_date'       => 'nullable|date',
            'exp_date'          => 'required|date',
            'warkat_file'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'signed_document'   => 'nullable|file|mimes:pdf|max:10240',
            'notes'             => 'nullable|string|max:500',
        ], [
            'existing_bg_id.required_if' => 'Mohon pilih Bank Garansi yang akan di-adendum.',
            'warkat_file.required'       => 'File scan Bank Garansi asli wajib diunggah.',
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($request->customer_id);
            $nominal = (float) str_replace(['.', ','], ['', '.'], $request->bg_nominal);

            // Handle file uploads
            $warkatPath = $request->file('warkat_file')->store('bg_documents/warkat', 'public');
            $signedPath = $request->hasFile('signed_document') 
                        ? $request->file('signed_document')->store('bg_documents/signed', 'public') 
                        : null;

            // Generate Unique Form Code
            $prefix = ($request->submission_type === 'adendum') ? 'AD' : 'TB';
            $formCode = $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            // Link or create Recommendation placeholder for customer
            $rec = BgRecommendation::where('customer_id', $customer->id)->latest()->first();
            if (!$rec) {
                $rec = BgRecommendation::create([
                    'customer_id'          => $customer->id,
                    'average'              => 0,
                    'top'                  => 0,
                    'lead_time'            => 0,
                    'inflation'            => 0,
                    'credit_limit_updated' => $nominal,
                    'set_bg'               => $nominal,
                    'status'               => 'waiting_approval',
                    'token'                => Str::uuid(),
                    'notes'                => json_encode(['type' => 'sales_direct', 'submission_type' => $request->submission_type, 'sales_notes' => $request->notes]),
                    'created_by'           => Auth::id(),
                ]);
            }

            // Create BgSubmission
            $submission = BgSubmission::create([
                'bg_recommendation_id' => $rec->id,
                'form_code'            => $formCode,
                'submission_type'      => $request->submission_type,
                'bg_number'            => $request->bg_number,
                'bg_nominal'           => $nominal,
                'exp_date'             => $request->exp_date,
                'warkat_file_path'     => 'storage/' . $warkatPath,
                'signed_document_path' => $signedPath ? 'storage/' . $signedPath : null,
                'status'               => 'waiting_approval',
                'submitted_at'         => now(),
                'upload_completed_at'  => now(),
                'token'                => Str::random(60),
            ]);

            // Create BankGaransi record in status 'draft' (wajib validasi Bu Rita baru masuk active list)
            $newBg = BankGaransi::create([
                'customer_id'      => $customer->id,
                'bg_number'        => $request->bg_number,
                'bg_type'          => ($request->submission_type === 'adendum') ? 'existing' : 'new',
                'is_adendum'       => ($request->submission_type === 'adendum') ? 1 : 0,
                'base_bg_id'       => ($request->submission_type === 'adendum') ? $request->existing_bg_id : null,
                'bg_nominal'       => $nominal,
                'issued_date'      => $request->issued_date ?? now(),
                'exp_date'         => $request->exp_date,
                'status'           => 'draft', // stays draft until Bu Rita validates
                'warkat_file_path' => 'storage/' . $warkatPath,
                'created_by'       => Auth::id(),
            ]);

            $newBg->details()->create([
                'bank_name'   => $request->bank_name,
                'branch_name' => $request->branch_name ?? '',
                'nominal'     => $nominal,
            ]);

            $typeName = ($request->submission_type === 'adendum') ? 'Adendum BG' : 'Tambah BG';
            $salesName = Auth::user()->name ?? 'Sales';

            // Notify Bu Rita (secretary-finance) & dispatch email approval
            try {
                $approvers = User::role(['secretary-finance', 'admin-rtm'])->get();
                if ($approvers->isEmpty()) {
                    $approvers = User::role(['super-admin'])->get();
                }

                Notification::send($approvers, new SystemNotification(
                    "Pengajuan {$typeName} dari Sales",
                    "Sales <b>{$salesName}</b> telah mengajukan {$typeName} untuk customer <b>{$customer->name}</b> ({$formCode}). Menunggu validasi Secretary Finance (Bu Rita).",
                    route('bg-approvals.index'),
                    'ph-paper-plane-tilt',
                    'warning'
                ));

                // Dispatch Email Approval for Bu Rita (secretary-finance)
                $rita = User::role('secretary-finance')->first();
                if (!$rita) {
                    $rita = User::role(['manager-finance', 'head-finance'])->first();
                }

                if ($rita) {
                    $approvalLog = ApprovalLog::create([
                        'category'     => 'BG',
                        'sub_category' => 'Lampiran D',
                        'related_id'   => $submission->id,
                        'approver_nik' => $rita->nik,
                        'status'       => 'Pending',
                        'level'        => 1,
                        'token'        => Str::random(60),
                    ]);

                    ProcessFinanceApprovalEmail::dispatch($approvalLog, $submission);
                }
            } catch (\Exception $e) {
                // notification error handled gracefully
            }

            DB::commit();

            return redirect()->route('sales-submissions.index')->with('success', "Pengajuan {$typeName} ({$formCode}) berhasil dikirim. Menunggu validasi dari Bu Rita.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan pengajuan: ' . $e->getMessage());
        }
    }
}
