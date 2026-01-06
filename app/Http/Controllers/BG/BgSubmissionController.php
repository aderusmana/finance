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
use Illuminate\Support\Str;

class BgSubmissionController extends Controller
{
    use ApprovalTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BgSubmission::with(['recommendation.customer']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function($row){
                    $customerName = $row->recommendation->customer->name ?? '-';
                    $siblingSubmissions = BgSubmission::where('bg_recommendation_id', $row->bg_recommendation_id)
                                            ->where('created_at', $row->created_at)
                                            ->orderBy('id', 'asc')
                                            ->pluck('id')
                                            ->toArray();

                    $myIndex = array_search($row->id, $siblingSubmissions);
                    $candidateBgs = BankGaransi::where('customer_id', $row->recommendation->customer_id)
                                        ->where('created_at', $row->created_at)
                                        ->where(function($q) {
                                            $q->where('bg_type', 'new')
                                              ->orWhere('status', 'draft');
                                        })
                                        ->orderBy('id', 'asc')
                                        ->with('details')
                                        ->get();

                    $bg = isset($candidateBgs[$myIndex]) ? $candidateBgs[$myIndex] : null;

                    if (!$bg) {
                        $bg = $candidateBgs->first();
                    }

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
                            <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-2">
                                <i class="ph-bold ph-bank me-1"></i> '.$bankName.'
                            </span>
                            <span class="fw-bold text-success small">Rp '.$nominal.'</span>
                        </div>
                    </div>';
                })
                ->addColumn('file', function($row){
                    if($row->signed_document_path) {
                        $url = asset($row->signed_document_path);

                        return '
                        <button type="button"
                                class="status-badge-lg bg-primary text-light fw-bold btn-view-file shadow-sm px-3"
                                data-url="'.$url.'"
                                data-id="'.$row->id.'"
                                data-bs-toggle="tooltip"
                                title="Klik untuk Melihat Dokumen & Proses Approval">
                            <i class="ph-bold ph-file-search me-1"></i> Review & Process
                        </button>';
                    }

                    return '<span class="status-badge-lg bg-secondary border border-secondary border-opacity-25">
                                <i class="ph-bold ph-file"></i> Belum Ada File
                            </span>';
                })
                ->addColumn('status', function($row){
                    $color = 'secondary';
                    if($row->status === 'uploaded') $color = 'info'; $icon = '<i class="ph-bold ph-upload-simple me-1"></i>';
                    if($row->status === 'completed') $color = 'success'; $icon = '<i class="ph-bold ph-check-circle me-1"></i>';
                    if($row->status === 'awaiting_upload') $color = 'warning'; $icon = '<i class="ph-bold ph-hourglass me-1"></i>';
                    return '<span class="status-badge-lg bg-'.$color.' text-light border btn-status"
                                data-id="'.$row->id.'">
                                '.$icon.' '.ucwords(str_replace('_', ' ', $row->status)).'
                            </span>';
                })
                ->addColumn('action', function ($row) {
                    return '<button class="status-badge-lg bg-warning text-light border btn-edit-submission"
                                    data-id="'.$row->id.'"
                                    data-bs-toggle="tooltip"
                                    title="Edit Data Administrasi (No Approval)">
                                <i class="ph-bold ph-pencil-simple"></i>
                            </button>';
                })
                ->rawColumns(['customer_name', 'file', 'status', 'action'])
                ->make(true);
        }

        $recommendations = BgRecommendation::with('customer')
            ->whereHas('customer')
            ->get();

        return view('page.bg.bg_submissions.index', compact('recommendations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bg_recommendation_id' => 'required',
            'form_code' => 'required|unique:bg_submissions,form_code',
        ]);

        $data = $request->except(['total_nominal']);
        if ($request->hasFile('signed_document')) {
            $path = $request->file('signed_document')->store('bg_documents/signed', 'public');
            $data['signed_document_path'] = 'storage/' . $path;
            $data['status'] = 'uploaded';
            $data['upload_completed_at'] = now();
            $data['submitted_at'] = now();
        } else {
            $data['status'] = 'pending_print';
        }

        if(!isset($data['token'])) {
            $data['token'] = Str::random(60);
        }

        BgSubmission::create($data);
        return response()->json(['success' => true, 'message' => 'Submission created!']);
    }

    public function update(Request $request, $id)
    {
         $sub = BgSubmission::findOrFail($id);

         if ($request->hasFile('signed_document')) {
             $request->validate([
                 'signed_document' => 'mimes:pdf,jpg,jpeg,png|max:5120',
             ]);
         }

         $data = $request->except(['total_nominal']);

         if ($request->hasFile('signed_document')) {

            $path = $request->file('signed_document')->store('bg_documents/signed', 'public');
            $data['signed_document_path'] = 'storage/' . $path;

            $data['status'] = 'uploaded';
            $data['upload_completed_at'] = now();

            if(!$sub->submitted_at) {
                $data['submitted_at'] = now();
            }
         }

         $sub->update($data);
         return response()->json(['success' => true, 'message' => 'Updated successfully!']);
    }

    public function getEditData($id)
    {
        $submission = BgSubmission::with(['recommendation.customer'])->findOrFail($id);
        $rec = $submission->recommendation;
        $customer = $rec->customer;

        $bgs = BankGaransi::where('customer_id', $customer->id)
                ->where('status', 'draft')
                ->with('details')
                ->get();

        if ($bgs->isEmpty()) {
            $bgs = BankGaransi::where('customer_id', $customer->id)
                ->latest()
                ->take(1)
                ->with('details')
                ->get();
        }

        $totalBgDiserahkan = $bgs->sum('bg_nominal');

        $allDetails = [];
        foreach($bgs as $bg) {
            foreach($bg->details as $detail) {
                $detail->parent_bg_id = $bg->id;
                $allDetails[] = $detail;
            }
        }

        $periodeString = '-';
        if ($rec->periods && $rec->periods->count() > 0) {
            $start = $rec->periods->min('period_date');
            $end   = $rec->periods->max('period_date');

            if ($start && $end) {
                \Carbon\Carbon::setLocale('id');
                $periodeString = \Carbon\Carbon::parse($start)->isoFormat('MMMM Y') . ' - ' . \Carbon\Carbon::parse($end)->isoFormat('MMMM Y');
            }
        }

        $firstBgId = $bgs->first() ? $bgs->first()->id : null;

        $data = [
            'submission_id' => $submission->id,
            'bg_id' => $firstBgId,
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
            'details' => $allDetails
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function processReview(Request $request, $id)
    {
        $submission = BgSubmission::with(['recommendation.customer'])->findOrFail($id);

        if ($request->action_type == 'edit_submit') {

            $approvalPathExists = ApprovalPath::where('category', 'BG')
                                    ->where('sub_category', 'Lampiran D')
                                    ->exists();

            if (!$approvalPathExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal: Approval Path untuk "BG - Lampiran D" belum dibuat.'
                ]);
            }

            DB::beginTransaction();
            try {
                $rec = $submission->recommendation;
                $customer = $rec->customer;

                $customer->update([
                    'name' => $request->nama_distributor,
                    'city' => $request->kota,
                    'area' => $request->wilayah_kerja,
                ]);

                $rec->update([
                    'average' => $request->rata_rata_penjualan,
                    'top' => $request->syarat_pembayaran,
                    'lead_time' => $request->lead_time,
                    'inflation' => $request->faktor_fluktuasi,
                    'credit_limit_updated' => $request->limit_kredit,
                    'set_bg' => $request->nilai_bg_ditetapkan,
                ]);

                if(isset($request->details)) {
                    foreach ($request->details as $detailId => $val) {
                        $detailObj = BgDetail::findOrFail($detailId);

                        $detailObj->update([
                            'bank_name' => $val['bank_name'],
                            'branch_name' => $val['branch_name'],
                            'nominal' => $val['nominal'],
                        ]);

                        $parentBg = BankGaransi::find($detailObj->bank_garansi_id);
                        if ($parentBg) {
                            $parentBg->update([
                                'bg_nominal' => $val['nominal']
                            ]);
                        }
                    }
                }

                $lampiranD = LampiranD::firstOrCreate(
                    ['bg_submission_id' => $submission->id],
                    ['version_latest' => 0, 'created_by' => auth()->id()]
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
                    'generated_by'  => auth()->id(),
                    'generated_at'  => now(),
                    'remarks'       => 'Correction via Submission Edit (Submission ID: '.$submission->id.')'
                ]);

                $lampiranD->update([
                    'version_latest'    => $nextVersion,
                    'active_version_id' => $newVersion->id
                ]);

                $requester = auth()->user();
                $logs = $this->generateApprovalLogs($requester, $submission->id, 'BG', 'Lampiran D');

                if ($logs->isEmpty()) {
                    throw new \Exception("User Role Manager Finance tidak ditemukan untuk approval.");
                }

                $submission->update(['status' => 'waiting_approval']);

                $firstLog = ApprovalLog::where('category', 'BG')
                    ->where('related_id', $submission->id)
                    ->where('status', 'Pending')
                    ->orderBy('level', 'asc')
                    ->first();

                if ($firstLog) {
                    ProcessFinanceApprovalEmail::dispatch($firstLog, $submission);
                }

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Data Lampiran D berhasil diperbarui, disimpan ke history version, & diteruskan ke Finance.']);

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

                $submissionDates = BgSubmission::where('bg_recommendation_id', $rec->id)->pluck('created_at');
                $bgs = BankGaransi::where('customer_id', $customer->id)
                        ->whereIn('created_at', $submissionDates)
                        ->with('details')
                        ->get();

                $totalBgDiserahkan = $bgs->sum('bg_nominal');

                $periodeString = '-';
                if ($rec->periods && $rec->periods->count() > 0) {
                    $start = $rec->periods->min('period_date');
                    $end   = $rec->periods->max('period_date');
                    if ($start && $end) {
                        \Carbon\Carbon::setLocale('id');
                        $periodeString = \Carbon\Carbon::parse($start)->isoFormat('MMMM Y') . ' - ' . \Carbon\Carbon::parse($end)->isoFormat('MMMM Y');
                    }
                }

                $detailsSnapshot = [];
                foreach($bgs as $bgItem) {
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

                $lampiranD = LampiranD::firstOrCreate(
                    ['bg_submission_id' => $submission->id],
                    ['version_latest' => 0, 'created_by' => auth()->id()]
                );

                $nextVersion = $lampiranD->version_latest + 1;

                $newVersion = LampiranDVersion::create([
                    'lampiran_d_id' => $lampiranD->id,
                    'version_no'    => $nextVersion,
                    'data_snapshot' => $snapshotData,
                    'file_path'     => $submission->signed_document_path,
                    'generated_by'  => auth()->id(),
                    'generated_at'  => now(),
                    'remarks'       => 'Direct Approved by Admin (Auto Generated Snapshot)'
                ]);

                $lampiranD->update([
                    'version_latest'    => $nextVersion,
                    'active_version_id' => $newVersion->id
                ]);

                foreach($bgs as $bg) {
                    if($bg->status == 'draft' || $bg->status == 'submitted') {
                        $bg->update([
                            'status'      => 'approved',
                            'issued_date' => now(),
                            'exp_date'    => now()->addYear(),
                        ]);
                        $this->addToBgHistory($submission, $bg);
                    }
                }

                $submission->update([
                    'status' => 'completed',
                    'token'  => Str::random(60)
                ]);

                if ($submission->recommendation) {
                    $submission->recommendation->update(['status' => 'approved']);
                }

                $this->sendCompletionEmails($submission);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Dokumen disetujui, Lampiran D terbit & BG Aktif.']);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid Action']);
    }

    private function addToBgHistory($submission, $currentBg)
    {
        // Cari BG sebelumnya (untuk referensi history pergerakan nominal)
        $prevBg = BankGaransi::where('customer_id', $currentBg->customer_id)
                    ->where('id', '<', $currentBg->id)
                    ->where('status', '!=', 'draft') // Jangan bandingkan dengan sesama draft
                    ->orderBy('id', 'desc')
                    ->first();

        $remarks = null;
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
            'remarks'           => $remarks ?? 'Direct Submitted by Admin',
            'created_by'        => auth()->id() ?? null
        ]);
    }

    public function show($id)
    {
        return response()->json(BgSubmission::with('recommendation')->findOrFail($id));
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

    private function sendCompletionEmails($submission) {
        $pendingSiblings = BgSubmission::where('bg_recommendation_id', $submission->bg_recommendation_id)
                            ->where('id', '!=', $submission->id)
                            ->where('status', '!=', 'completed')
                            ->where('status', '!=', 'approved')
                            ->count();
        
        if ($pendingSiblings > 0) {
            return;
        }

        $customerEmail = $submission->recommendation->customer->email;
        $salesEmails = User::role('head-SNM')->pluck('email')->toArray();
        $financeEmails = User::role('head-finance')->pluck('email')->toArray();

        $allRecipients = array_merge([$customerEmail], $salesEmails, $financeEmails);
        $recipients = array_unique(array_filter($allRecipients));

        foreach($recipients as $email) {
            if (!empty($email)) {
                Mail::to($email)->queue(new CustomerBgReadyMail($submission));
            }
        }
    }
}
