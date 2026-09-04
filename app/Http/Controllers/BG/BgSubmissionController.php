<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgSubmission;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BgDetail;
use Illuminate\Support\Facades\Mail;
use App\Models\BG\BankGaransi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApprovalTrait;
use App\Models\Master\ApprovalLog;
use App\Jobs\ProcessFinanceApprovalEmail;
use App\Mail\CustomerBgReadyMail;
use App\Models\BG\LampiranD;
use App\Models\BG\LampiranDVersion;
use App\Models\Master\ApprovalPath;
use App\Models\User;
use App\Models\BG\BgHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Customer\CreditLimit;
use App\Mail\CreditLimitUpdatedItMail;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class BgSubmissionController extends Controller
{
    use ApprovalTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->input('type', 'active');

            $query = BgSubmission::with(['recommendation.customer']);

            if ($type === 'history') {
                $query->whereIn('status', ['completed', 'approved'])
                      ->orderBy('updated_at', 'desc');
            } else {
                $query->whereNotIn('status', ['completed', 'approved']);

                if ($request->has('status_filter') && $request->status_filter != 'all') {
                    $query->where('status', $request->status_filter);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function($row){
                    return $this->generateCustomerColumn($row);
                })
                ->addColumn('form_code', function($row){
                    return '<span class="fw-bold text-primary">'.$row->form_code.'</span>';
                })
                ->addColumn('date_info', function($row) use ($type) {
                    if ($type === 'history') {
                        return '<div class="d-flex flex-column">
                                    <span class="text-muted small">Completed:</span>
                                    <span class="fw-bold text-success">'.$row->updated_at->format('d M Y').'</span>
                                    <span class="text-muted" style="font-size:10px">'.$row->updated_at->format('H:i').'</span>
                                </div>';
                    } else {
                        return '<div class="d-flex flex-column">
                                    <span class="text-muted small">Created:</span>
                                    <span class="fw-bold text-dark">'.($row->created_at ? $row->created_at->format('d M Y') : '-').'</span>
                                </div>';
                    }
                })
                ->addColumn('file', function($row) use ($type){
                    if($row->signed_document_path) {
                        $url = asset($row->signed_document_path);
                        if ($type === 'history') {
                            return '
                            <button type="button"
                                    class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3 btn-view-file shadow-sm"
                                    data-url="'.$url.'"
                                    data-id="'.$row->id.'"
                                    data-status="completed"
                                    title="View Final Document">
                                <i class="ph-bold ph-check-circle me-1"></i> View Document
                            </button>';
                        }

                        if ($row->status === 'uploaded') {
                            return '
                            <button type="button"
                                    class="status-badge-lg bg-info text-light fw-bold btn-view-file shadow-sm px-3"
                                    data-url="'.$url.'"
                                    data-id="'.$row->id.'"
                                    data-status="uploaded"
                                    data-bs-toggle="tooltip"
                                    title="Verifikasi Dokumen Upload Customer">
                                <i class="ph-bold ph-file-search me-1"></i> Verifikasi Upload
                            </button>';
                        }

                        if ($row->status === 'waiting_sales_input') {
                            return '
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <button type="button"
                                        class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 btn-input-sales shadow-sm"
                                        data-id="'.$row->id.'"
                                        title="Isi No BG, Exp Date & Upload Warkat">
                                    <i class="ph-bold ph-pencil-simple me-1"></i> Lengkapi Data BG
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-light border rounded-circle btn-view-file shadow-sm"
                                        data-url="'.$url.'"
                                        data-id="'.$row->id.'"
                                        data-status="waiting_sales_input"
                                        title="Lihat Dokumen TTD">
                                    <i class="ph-bold ph-eye"></i>
                                </button>
                            </div>';
                        }

                        if ($row->status === 'waiting_approval') {
                            return '
                            <button type="button"
                                    class="status-badge-lg bg-primary text-light fw-bold btn-view-file shadow-sm px-3"
                                    data-url="'.$url.'"
                                    data-id="'.$row->id.'"
                                    data-status="waiting_approval"
                                    data-bs-toggle="tooltip"
                                    title="Menunggu Validasi Finance (Bu Rita)">
                                <i class="ph-bold ph-hourglass-medium me-1"></i> Waiting Finance
                            </button>';
                        }

                        return '
                        <button type="button"
                                class="status-badge-lg bg-primary text-light fw-bold btn-view-file shadow-sm px-3"
                                data-url="'.$url.'"
                                data-id="'.$row->id.'"
                                data-status="process"
                                data-bs-toggle="tooltip"
                                title="Review Document & Process">
                            <i class="ph-bold ph-file-search me-1"></i> Review & Process
                        </button>';
                    }
                    return '<span class="status-badge-lg bg-secondary border border-secondary border-opacity-25"><i class="ph-bold ph-file"></i> No File</span>';
                })
                ->addColumn('status', function($row){
                    $color = 'secondary';
                    $icon = 'circle';
                    $label = ucwords(str_replace('_', ' ', $row->status));

                    if($row->status === 'uploaded') { 
                        $color = 'info'; 
                        $icon = 'upload-simple'; 
                        $label = 'Uploaded (Need Verification)';
                    }
                    if($row->status === 'waiting_sales_input') { 
                        $color = 'warning'; 
                        $icon = 'pencil-simple-line'; 
                        $label = 'Waiting Sales Input';
                    }
                    if($row->status === 'waiting_approval') { 
                        $color = 'primary'; 
                        $icon = 'hourglass-medium'; 
                        $label = 'Waiting Finance (Bu Rita)';
                    }
                    if($row->status === 'awaiting_upload') { 
                        $color = 'warning'; 
                        $icon = 'hourglass'; 
                    }
                    if($row->status === 'completed' || $row->status === 'approved') { 
                        $color = 'success'; 
                        $icon = 'check-circle'; 
                        $label = 'Completed';
                    }
                    if($row->status === 'pending_print') { 
                        $color = 'secondary'; 
                        $icon = 'printer'; 
                    }

                    return '<span class="status-badge-lg bg-'.$color.' text-light border btn-status" data-id="'.$row->id.'">
                                <i class="ph-bold ph-'.$icon.' me-1"></i> '.$label.'
                            </span>';
                })
                ->addColumn('action', function ($row) use ($type) {
                    $btn = '<div class="action-btn-group">';

                    if ($type === 'active') {
                        $btn .= '<button type="button" class="btn btn-secondary action-btn-hover btn-edit-submission" data-id="' . $row->id . '" title="Edit Admin">
                                    <i class="ph-bold ph-pencil-simple"></i>
                                 </button>
                                 <button type="button" class="btn btn-danger action-btn-hover btn-delete" data-id="' . $row->id . '" title="Delete">
                                    <i class="ph-bold ph-trash"></i>
                                 </button>';
                    } else if ($type === 'history') {
                        $btn .= '<span class="text-muted small"><i class="ph-bold ph-lock-key"></i> Locked</span>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['customer_name', 'form_code', 'date_info', 'file', 'status', 'action'])
                ->make(true);
        }

        $recommendations = BgRecommendation::with('customer')->whereHas('customer')->get();
        return view('page.bg.bg_submissions.index', compact('recommendations'));
    }

    private function generateCustomerColumn($row) {
        $customerName = $row->recommendation->customer->name ?? '-';

        $createdAt = $row->created_at;
        $candidateBgs = BankGaransi::where('customer_id', $row->recommendation->customer_id)
                            ->whereBetween('created_at', [
                                $createdAt->copy()->subMinutes(5),
                                $createdAt->copy()->addMinutes(5)
                            ])
                            ->orderBy('id', 'asc')
                            ->with('details')
                            ->get();

        if ($candidateBgs->isEmpty()) {
            $candidateBgs = BankGaransi::where('customer_id', $row->recommendation->customer_id)
                                ->latest()
                                ->take(3)
                                ->with('details')
                                ->get();
        }

        $badgeClass = in_array($row->status, ['completed', 'approved'])
                        ? 'bg-success bg-opacity-10 text-success border-success'
                        : 'bg-light text-primary border-primary-subtle';

        // Multi-Bank display
        if ($candidateBgs->count() > 1) {
            $totalNominal = $candidateBgs->sum('bg_nominal');
            $bgNumbers = $candidateBgs->pluck('bg_number')->filter()->implode(', ');

            $banksHtml = '';
            foreach ($candidateBgs as $b) {
                $bName = $b->details->first()->bank_name ?? 'Bank';
                $bNom = number_format($b->bg_nominal, 0, ',', '.');
                $banksHtml .= '<span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;">
                    <i class="ph-bold ph-bank text-primary me-1"></i><strong>'.$bName.'</strong>: Rp '.$bNom.'
                </span>';
            }

            return '
            <div class="d-flex flex-column">
                <div class="mb-1">
                    <span class="fw-bold text-dark">'.$customerName.'</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-2 small ms-1">
                        <i class="ph-bold ph-stack me-1"></i> Multi-Bank ('.$candidateBgs->count().' Bank)
                    </span>
                    '.($bgNumbers ? '<div class="text-muted" style="font-size: 11px;">Ref: '.$bgNumbers.'</div>' : '').'
                </div>
                <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                    '.$banksHtml.'
                </div>
                <div>
                    <span class="text-muted small">Total: </span>
                    <span class="fw-bold text-dark small">Rp '.number_format($totalNominal, 0, ',', '.').'</span>
                </div>
            </div>';
        }

        // Single Bank display
        $bg = $candidateBgs->first();
        $bgNumber = $bg ? $bg->bg_number : 'No BG Ref';
        $bankName = $bg && $bg->details->first() ? $bg->details->first()->bank_name : '-';
        $nominal  = $bg ? number_format($bg->bg_nominal, 0, ',', '.') : '0';

        return '
        <div class="d-flex flex-column">
            <div class="mb-1">
                <span class="fw-bold text-dark">'.$customerName.'</span>
                <span class="text-muted small ms-1"> - '.$bgNumber.'</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge '.$badgeClass.' border rounded-pill px-2">
                    <i class="ph-bold ph-bank me-1"></i> '.$bankName.'
                </span>
                <span class="fw-bold text-dark small">Rp '.$nominal.'</span>
            </div>
        </div>';
    }

    public function store(Request $request)
    {
        $request->validate([
            'bg_recommendation_id' => 'required',
            'form_code' => 'required|unique:bg_submissions,form_code',
        ]);

        $data = $request->except(['signed_document']);
        $data['status'] = 'pending_print';
        if(!isset($data['token'])) $data['token'] = Str::random(60);

        $submission = BgSubmission::create($data);

        if ($request->hasFile('signed_document')) {
            $file = $request->file('signed_document');
            $filename = 'Signed_' . $submission->form_code . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bg_documents/' . $submission->id, $filename, 'public');

            $submission->update([
                'signed_document_path' => 'storage/' . $path,
                'status' => 'uploaded',
                'upload_completed_at' => now(),
                'submitted_at' => now(),
            ]);
        }
        return response()->json(['success' => true, 'message' => 'Submission created!']);
    }

    public function update(Request $request, $id)
    {
         $sub = BgSubmission::findOrFail($id);
         if ($request->hasFile('signed_document')) {
             $request->validate(['signed_document' => 'mimes:pdf,jpg,jpeg,png|max:5120']);
         }
         $data = $request->except(['signed_document']);

         if ($request->hasFile('signed_document')) {
            if ($sub->signed_document_path) {
                $oldPath = str_replace('storage/', '', $sub->signed_document_path);
                Storage::disk('public')->delete($oldPath);
            }
            $file = $request->file('signed_document');
            $filename = 'Signed_' . $sub->form_code . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bg_documents/' . $sub->id, $filename, 'public');

            $data['signed_document_path'] = 'storage/' . $path;
            $data['status'] = 'uploaded';
            $data['upload_completed_at'] = now();
            if(!$sub->submitted_at) $data['submitted_at'] = now();
         }
         $sub->update($data);
         return response()->json(['success' => true, 'message' => 'Updated successfully!']);
    }

    public function destroy($id)
    {
        $sub = BgSubmission::findOrFail($id);
        if($sub->signed_document_path) {
            $relativePath = str_replace('storage/', '', $sub->signed_document_path);
            Storage::disk('public')->delete($relativePath);
        }
        $sub->delete();
        return response()->json(['success' => true, 'message' => 'Deleted!']);
    }

    public function show($id) {
        $sub = BgSubmission::with('recommendation')->findOrFail($id);
        $sub->file_exists = $sub->signed_document_path && file_exists(public_path($sub->signed_document_path));
        return response()->json($sub);
    }

    public function getEditData($id)
    {
        $submission = BgSubmission::with(['recommendation.customer'])->findOrFail($id);
        $rec = $submission->recommendation;
        $customer = $rec->customer;
        $metadata = json_decode($rec->notes, true) ?? [];
        $batchBgs = collect();

        if (isset($metadata['action']) && $metadata['action'] === 'existing' && !empty($metadata['target_bg_id'])) {
            $targetBg = BankGaransi::where('id', $metadata['target_bg_id'])
                        ->with('details')
                        ->first();
            if ($targetBg) $batchBgs->push($targetBg);
        }
        else {
            $createdAt = $submission->created_at;
            $batchBgs = BankGaransi::where('customer_id', $customer->id)
                                ->whereBetween('created_at', [
                                    $createdAt->copy()->subMinutes(5),
                                    $createdAt->copy()->addMinutes(5)
                                ])
                                ->with('details')
                                ->orderBy('id', 'asc')
                                ->get();
        }

        if ($batchBgs->isEmpty()) {
            $latestBg = BankGaransi::where('customer_id', $customer->id)
                ->where('status', 'draft')
                ->latest()
                ->with('details')
                ->first();
            if ($latestBg) $batchBgs->push($latestBg);
        }

        if ($batchBgs->isEmpty()) {
             return response()->json(['success' => false, 'message' => 'Bank Guarantee data not found (Timestamp mismatch & No ID).']);
        }

        $isMultiBank = $batchBgs->count() > 1;
        $totalBgDiserahkan = $batchBgs->sum('bg_nominal');
        $specificDetails = [];
        foreach($batchBgs as $bgItem) {
            foreach ($bgItem->details as $detail) {
                $detail->parent_bg_id = $bgItem->id;
                $detail->parent_bg_number = $bgItem->bg_number;
                $detail->parent_exp_date = $bgItem->exp_date ? Carbon::parse($bgItem->exp_date)->format('Y-m-d') : '';
                $detail->parent_warkat = $bgItem->warkat_file_path ? asset($bgItem->warkat_file_path) : null;
                $specificDetails[] = $detail;
            }
        }

        $periodeString = '-';
        if ($rec->periods && $rec->periods->count() > 0) {
            $start = $rec->periods->min('period_date');
            $end   = $rec->periods->max('period_date');

            if ($start && $end) {
                Carbon::setLocale('id');
                $periodeString = Carbon::parse($start)->isoFormat('MMMM Y') . ' - ' . Carbon::parse($end)->isoFormat('MMMM Y');
            }
        }

        $firstBg = $batchBgs->first();
        $bgNumber = $submission->bg_number ?? ($firstBg->bg_number ?? '');
        $expDate = $submission->exp_date ? Carbon::parse($submission->exp_date)->format('Y-m-d') : ($firstBg && $firstBg->exp_date ? Carbon::parse($firstBg->exp_date)->format('Y-m-d') : '');
        $warkatFileUrl = $submission->warkat_file_path ? asset($submission->warkat_file_path) : ($firstBg && $firstBg->warkat_file_path ? asset($firstBg->warkat_file_path) : null);

        $data = [
            'submission_id' => $submission->id,
            'bg_id' => $firstBg->id,
            'is_multi_bank' => $isMultiBank,
            'bg_count' => $batchBgs->count(),
            'bg_number' => $bgNumber,
            'exp_date' => $expDate,
            'warkat_file_url' => $warkatFileUrl,
            'nama_distributor' => $customer->name,
            'kota' => $customer->city,
            'wilayah_kerja' => $customer->area ?? '-',
            'periode' => $periodeString,
            'rata_rata_penjualan' => $rec->average,
            'syarat_pembayaran' => $rec->top,
            'lead_time' => $rec->lead_time,
            'faktor_fluktuasi' => $rec->inflation,
            'limit_kredit' => $rec->credit_limit_updated,
            'nilai_bg_ditetapkan' => $rec->set_bg,
            'nilai_bg_diserahkan' => $totalBgDiserahkan,
            'details' => $specificDetails
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function processReview(Request $request, $id)
    {
        $submission = BgSubmission::with(['recommendation.customer'])->findOrFail($id);

        if ($request->action_type == 'edit_submit') {

            $approvalPathExists = ApprovalPath::where('category', 'BG')->where('sub_category', 'Lampiran D')->exists();
            if (!$approvalPathExists) return response()->json(['success' => false, 'message' => 'Approval Path not created yet.']);

            DB::beginTransaction();
            try {
                $rec = $submission->recommendation;
                $customer = $rec->customer;
                $customer->update(['name' => $request->nama_distributor, 'city' => $request->kota, 'area' => $request->wilayah_kerja]);

                $oldRecData = [
                    'limit' => $rec->credit_limit_updated,
                    'set_bg' => $rec->set_bg
                ];

                $rec->update([
                    'average' => $request->rata_rata_penjualan,
                    'top' => $request->syarat_pembayaran,
                    'lead_time' => $request->lead_time,
                    'inflation' => $request->faktor_fluktuasi,
                    'credit_limit_updated' => $request->limit_kredit,
                    'set_bg' => $request->nilai_bg_ditetapkan
                ]);

                if(isset($request->details)) {
                    foreach ($request->details as $detailId => $val) {
                        $detailObj = BgDetail::findOrFail($detailId);
                        $detailObj->update(['bank_name' => $val['bank_name'], 'branch_name' => $val['branch_name'], 'nominal' => $val['nominal']]);
                        $parentBg = BankGaransi::find($detailObj->bank_garansi_id);
                        if ($parentBg) {
                            $parentBgUpdate = ['bg_nominal' => $val['nominal']];
                            if (!empty($val['bg_number'])) {
                                $parentBgUpdate['bg_number'] = $val['bg_number'];
                            }
                            $parentBg->update($parentBgUpdate);
                        }
                    }
                }

                $lampiranD = LampiranD::firstOrCreate(
                    ['bg_submission_id' => $submission->id],
                    ['version_latest' => 0, 'created_by' => Auth::id()]
                );

                $snapshotData = [
                    'nama_distributor' => $request->nama_distributor,
                    'kota' => $request->kota,
                    'wilayah_kerja' => $request->wilayah_kerja,
                    'periode' => $request->periode,
                    'rata_rata_penjualan' => $request->rata_rata_penjualan,
                    'syarat_pembayaran' => $request->syarat_pembayaran,
                    'lead_time' => $request->lead_time,
                    'faktor_fluktuasi' => $request->faktor_fluktuasi,
                    'limit_kredit' => $request->limit_kredit,
                    'nilai_bg_ditetapkan' => $request->nilai_bg_ditetapkan,
                    'nilai_bg_diserahkan' => $request->nilai_bg_diserahkan,
                    'details' => $request->details
                ];

                $nextVersion = $lampiranD->version_latest + 1;

                $newVersion = LampiranDVersion::create([
                    'lampiran_d_id' => $lampiranD->id,
                    'version_no'    => $nextVersion,
                    'data_snapshot' => $snapshotData,
                    'file_path'     => $submission->signed_document_path,
                    'generated_by'  => Auth::id(),
                    'generated_at'  => now(),
                    'remarks'       => 'Correction via Submission Edit (Submission ID: '.$submission->id.')'
                ]);

                $lampiranD->update([
                    'version_latest'    => $nextVersion,
                    'active_version_id' => $newVersion->id
                ]);

                $requester = auth()->user();
                $Logs = $this->generateApprovalLogs($requester, $submission->id, 'BG', 'Lampiran D');

                $subUpdate = ['status' => 'waiting_approval'];
                if ($request->filled('bg_number')) {
                    $subUpdate['bg_number'] = $request->bg_number;
                }
                if ($request->filled('exp_date')) {
                    $subUpdate['exp_date'] = $request->exp_date;
                }
                if ($request->hasFile('warkat_file')) {
                    $wFile = $request->file('warkat_file');
                    $wFilename = 'Warkat_' . $submission->form_code . '_' . time() . '.' . $wFile->getClientOriginalExtension();
                    $wPath = $wFile->storeAs('bg_documents/warkat', $wFilename, 'public');
                    $subUpdate['warkat_file_path'] = 'storage/' . $wPath;
                }
                $submission->update($subUpdate);

                // Sync BG data to BankGaransi batch records
                $createdAt = Carbon::parse($submission->created_at);
                $batchBgs = BankGaransi::where('customer_id', $customer->id)
                            ->whereBetween('created_at', [
                                $createdAt->copy()->subMinutes(5),
                                $createdAt->copy()->addMinutes(5)
                            ])
                            ->get();

                if ($batchBgs->isEmpty()) {
                    $batchBgs = BankGaransi::where('customer_id', $customer->id)->latest()->take(3)->get();
                }

                foreach ($batchBgs as $bgItem) {
                    $bgUpdateData = [];
                    if ($request->filled('bg_number') && $batchBgs->count() === 1) {
                        $bgUpdateData['bg_number'] = $request->bg_number;
                    }
                    if ($request->filled('exp_date')) {
                        $bgUpdateData['exp_date'] = $request->exp_date;
                    }
                    if (isset($subUpdate['warkat_file_path'])) {
                        $bgUpdateData['warkat_file_path'] = $subUpdate['warkat_file_path'];
                    }
                    if (!empty($bgUpdateData)) {
                        $bgItem->update($bgUpdateData);
                    }
                }

                // Bu Rita (secretary-finance) validation log
                $rita = User::role('secretary-finance')->first();
                if (!$rita) {
                    $rita = User::role(['manager-finance', 'head-finance'])->first();
                }

                if ($rita) {
                    $existingLog = ApprovalLog::where('category', 'BG')
                        ->where('related_id', $submission->id)
                        ->where('approver_nik', $rita->nik)
                        ->where('status', 'Pending')
                        ->first();

                    if (!$existingLog) {
                        $newLog = ApprovalLog::create([
                            'category'      => 'BG',
                            'sub_category'  => 'Lampiran D',
                            'related_id'    => $submission->id,
                            'approver_nik'  => $rita->nik,
                            'approver_name' => $rita->name,
                            'status'        => 'Pending',
                            'level'         => 1,
                            'token'         => Str::random(60),
                        ]);

                        ProcessFinanceApprovalEmail::dispatch($newLog, $submission);
                    }
                }

                $firstLog = ApprovalLog::where('category', 'BG')
                    ->where('related_id', $submission->id)
                    ->where('status', 'Pending')
                    ->orderBy('level', 'asc')
                    ->first();

                if ($firstLog && (!$rita || $firstLog->approver_nik !== $rita->nik)) {
                    ProcessFinanceApprovalEmail::dispatch($firstLog, $submission);
                }

                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($submission)
                    ->useLog('bg_submission')
                    ->event('review_correction')
                    ->withProperties([
                        'form_code' => $submission->form_code,
                        'customer' => $customer->name,
                        'changes' => [
                            'credit_limit' => [
                                'from' => $oldRecData['limit'],
                                'to' => $request->limit_kredit
                            ],
                            'set_bg' => [
                                'from' => $oldRecData['set_bg'],
                                'to' => $request->nilai_bg_ditetapkan
                            ]
                        ],
                        'approval_status' => 'waiting_finance'
                    ])
                    ->Log("Tim Sales / Admin melengkapi data Bank Garansi (No: {$request->bg_number}, Exp: {$request->exp_date}) dan meneruskan untuk validasi Bu Rita (Finance)");

                $approvers = User::role(['secretary-finance', 'manager-finance', 'head-finance'])->get();
                Notification::send($approvers, new SystemNotification(
                    'Approval Required (Bu Rita)',
                    "Kelengkapan Bank Garansi untuk <b>{$customer->name}</b> ({$submission->form_code}) telah diisi oleh tim Sales dan menunggu validasi Anda.",
                    route('bg-approvals.index'),
                    'ph-signature',
                    'warning'
                ));

                $admins = User::role(['super-admin'])->get();
                Notification::send($admins, new SystemNotification(
                    'Submission Forwarded',
                    "Attachment D for <b>{$customer->name}</b> was forwarded to Finance.",
                    route('bg-submissions.index'),
                    'ph-paper-plane-tilt',
                    'info'
                ));

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Data corrected & forwarded to Finance (Log Recorded).']);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }

        if ($request->action_type == 'direct_submit') {

            DB::beginTransaction();
            try {
                $rec = $submission->recommendation;
                $customer = $rec->customer;
                $metadata = json_decode($rec->notes, true) ?? [];

                $targetBgToUpdate = null;
                $allBatchBgs = collect();

                if (isset($metadata['action']) && $metadata['action'] === 'existing' && !empty($metadata['target_bg_id'])) {
                    $targetBgToUpdate = BankGaransi::where('id', $metadata['target_bg_id'])->with('details')->first();
                    if($targetBgToUpdate) $allBatchBgs->push($targetBgToUpdate);
                }
                else {
                    $createdAt = $submission->created_at;
                    $allBatchBgs = BankGaransi::where('customer_id', $customer->id)
                            ->whereBetween('created_at', [
                                $createdAt->copy()->subMinutes(5),
                                $createdAt->copy()->addMinutes(5)
                            ])
                            ->with('details')
                            ->orderBy('id', 'asc')
                            ->get();

                    if ($allBatchBgs->isEmpty()) {
                        $allBatchBgs = BankGaransi::where('customer_id', $customer->id)->latest()->take(3)->with('details')->get();
                    }

                    $targetBgToUpdate = $allBatchBgs->first();
                }

                if ($allBatchBgs->isEmpty() || !$targetBgToUpdate) {
                    throw new \Exception("Bank Guarantee data not found.");
                }

                $totalBgDiserahkan = $allBatchBgs->sum('bg_nominal');

                $periodeString = '-';
                if ($rec->periods && $rec->periods->count() > 0) {
                    $start = $rec->periods->min('period_date');
                    $end   = $rec->periods->max('period_date');
                    if ($start && $end) {
                        Carbon::setLocale('id');
                        $periodeString = Carbon::parse($start)->isoFormat('MMMM Y') . ' - ' . Carbon::parse($end)->isoFormat('MMMM Y');
                    }
                }

                $detailsSnapshot = [];
                foreach($allBatchBgs as $bgItem) {
                    foreach($bgItem->details as $d) {
                        $detailsSnapshot[$d->id] = [
                            'bank_name' => $d->bank_name,
                            'branch_name' => $d->branch_name,
                            'nominal' => $d->nominal,
                            'id' => $d->id
                        ];
                    }
                }

                $snapshotData = [
                    'nama_distributor' => $customer->name,
                    'kota' => $customer->city,
                    'wilayah_kerja' => $customer->area ?? '-',
                    'periode' => $periodeString,
                    'rata_rata_penjualan' => $rec->average,
                    'syarat_pembayaran' => $rec->top,
                    'lead_time' => $rec->lead_time,
                    'faktor_fluktuasi' => $rec->inflation,
                    'limit_kredit' => $rec->credit_limit_updated,
                    'nilai_bg_ditetapkan' => $rec->set_bg,
                    'nilai_bg_diserahkan' => $totalBgDiserahkan,
                    'details' => $detailsSnapshot
                ];

                $lampiranD = LampiranD::firstOrCreate(['bg_submission_id' => $submission->id], ['version_latest' => 0, 'created_by' => Auth::id()]);
                $nextVersion = $lampiranD->version_latest + 1;
                $newVersion = LampiranDVersion::create([
                    'lampiran_d_id' => $lampiranD->id,
                    'version_no'    => $nextVersion,
                    'data_snapshot' => $snapshotData,
                    'file_path'     => $submission->signed_document_path,
                    'generated_by'  => Auth::id(),
                    'generated_at'  => now(),
                    'remarks'       => 'Upload verified by Admin-RTM. Forwarded to Sales for BG details completion.'
                ]);
                $lampiranD->update(['version_latest' => $nextVersion, 'active_version_id' => $newVersion->id]);

                // Update submission status to waiting_sales_input
                $submission->update([
                    'status'       => 'waiting_sales_input',
                    'reviewed_at'  => now(),
                    'validated_by' => Auth::id(),
                ]);

                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($submission)
                    ->useLog('bg_submission')
                    ->event('verify_upload')
                    ->withProperties([
                        'form_code'       => $submission->form_code,
                        'customer'        => $customer->name,
                        'lampiran_d_ver'  => $nextVersion,
                        'status'          => 'waiting_sales_input'
                    ])
                    ->Log("Admin-RTM memverifikasi hasil upload dokumen konfirmasi ({$submission->form_code}). Pengajuan diteruskan ke tim Sales untuk melengkapi nomor BG, expired date, nominal, dan warkat.");

                // Notify Sales (role sales, dep-SNM, and recommendation approver)
                $salesUsers = User::role(['sales', 'dep-SNM'])->get();
                if ($rec && $rec->sales_approved_by) {
                    $specificSales = User::find($rec->sales_approved_by);
                    if ($specificSales && !$salesUsers->contains('id', $specificSales->id)) {
                        $salesUsers->push($specificSales);
                    }
                }

                if ($salesUsers->isNotEmpty()) {
                    Notification::send($salesUsers, new SystemNotification(
                        'Upload Diverifikasi: Silakan Lengkapi Data BG',
                        "Dokumen konfirmasi Bank Garansi untuk <b>{$customer->name}</b> ({$submission->form_code}) telah diverifikasi oleh Admin-RTM. Silakan tim Sales melengkapi Nomor BG, Expired Date, Nominal, dan Upload scan warkat Bank Garansi.",
                        route('bg-submissions.index'),
                        'ph-pencil-simple-line',
                        'warning'
                    ));
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diverifikasi! Notifikasi telah dikirim ke tim Sales untuk melengkapi nomor BG, expired date, nominal, dan scan warkat.'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid Action']);
    }

    private function addToBgHistory($submission, $currentBg, $customRemarks = null)
    {
        $prevBg = BankGaransi::where('customer_id', $currentBg->customer_id)
                    ->where('id', '<', $currentBg->id)
                    ->where('status', '!=', 'draft')
                    ->orderBy('id', 'desc')
                    ->first();

        $remarks = $customRemarks;
        if(!$remarks) {
            $remarks = 'Direct Submitted by Admin';
            $lampiranD = LampiranD::where('bg_submission_id', $submission->id)->with('activeVersion')->first();
            if ($lampiranD && $lampiranD->activeVersion) {
                $remarks = $lampiranD->activeVersion->remarks;
            }
        }

        BgHistory::create([
            'bank_garansi_id'   => $currentBg->id,
            'previous_nominal'  => $prevBg ? $prevBg->bg_nominal : 0,
            'new_nominal'       => $currentBg->bg_nominal,
            'previous_exp_date' => $prevBg ? $prevBg->exp_date : null,
            'new_exp_date'      => $currentBg->exp_date,
            'remarks'           => $remarks,
            'created_by'        => Auth::id()
        ]);
    }

    private function sendCompletionEmails($submission)
    {
        $pendingSiblings = BgSubmission::where('bg_recommendation_id', $submission->bg_recommendation_id)
                            ->where('id', '!=', $submission->id)
                            ->where('status', '!=', 'completed')
                            ->where('status', '!=', 'approved')
                            ->count();

        if ($pendingSiblings > 0) {
            return;
        }

        try {
            $internalUsers = User::role(['super-admin', 'manager-finance', 'head-finance', 'secretary-finance', 'admin-rtm'])->get();
            $custName = $submission->recommendation->customer->name ?? 'Customer';

            Notification::send($internalUsers, new SystemNotification(
                'Dokumen Selesai',
                "Attachment D & BG for <b>{$custName}</b> have been issued & sent to the Customer.",
                route('bg-submissions.index', ['type' => 'history']),
                'ph-files',
                'success'
            ));
        } catch (\Exception $e) {
            Log::error("Gagal kirim notif sistem completion: " . $e->getMessage());
        }

        try {
            $rec = $submission->recommendation;
            $cust = $rec ? $rec->customer : null;

            // STRICT: Lampiran D dikirim HANYA ke admin-rtm dan manager purchasing
            $adminRtmEmails = User::role('admin-rtm')->pluck('email')->toArray();
            $purchasingEmail = ($cust && !empty($cust->purchasing_manager_email)) ? [$cust->purchasing_manager_email] : [];

            $allRecipients = array_merge($adminRtmEmails, $purchasingEmail);
            $recipients    = array_unique(array_filter($allRecipients, fn($e) => !empty($e) && filter_var($e, FILTER_VALIDATE_EMAIL)));

            foreach($recipients as $email) {
                Mail::to($email)->queue(new CustomerBgReadyMail($submission));
            }
        } catch (\Exception $e) {
            Log::error("Gagal kirim email completion: " . $e->getMessage());
        }
    }
}
