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
                                    <span class="text-muted small">Selesai:</span>
                                    <span class="fw-bold text-success">'.$row->updated_at->format('d M Y').'</span>
                                    <span class="text-muted" style="font-size:10px">'.$row->updated_at->format('H:i').'</span>
                                </div>';
                    } else {
                        return '<div class="d-flex flex-column">
                                    <span class="text-muted small">Dibuat:</span>
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
                                <i class="ph-bold ph-check-circle me-1"></i> Lihat Dokumen
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

                    if($row->status === 'uploaded') { $color = 'info'; $icon = 'upload-simple'; }
                    if($row->status === 'awaiting_upload') { $color = 'warning'; $icon = 'hourglass'; }
                    if($row->status === 'completed') { $color = 'success'; $icon = 'check-circle'; }
                    if($row->status === 'approved') { $color = 'success'; $icon = 'check-circle'; }
                    if($row->status === 'pending_print') { $color = 'secondary'; $icon = 'printer'; }

                    return '<span class="status-badge-lg bg-'.$color.' text-light border btn-status" data-id="'.$row->id.'">
                                <i class="ph-bold ph-'.$icon.' me-1"></i> '.ucwords(str_replace('_', ' ', $row->status)).'
                            </span>';
                })
                ->addColumn('action', function ($row) use ($type) {
                    if ($type === 'history') {
                        return '<span class="text-muted small"><i class="ph-bold ph-lock-key"></i> Locked</span>';
                    }
                    return '
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-sm btn-warning text-white btn-edit-submission" data-id="'.$row->id.'" title="Edit Admin"><i class="ph-bold ph-pencil-simple"></i></button>
                            <button class="btn btn-sm btn-danger text-white btn-delete" data-id="'.$row->id.'" title="Delete"><i class="ph-bold ph-trash"></i></button>
                        </div>
                    ';
                })
                ->rawColumns(['customer_name', 'form_code', 'date_info', 'file', 'status', 'action'])
                ->make(true);
        }

        $recommendations = BgRecommendation::with('customer')->whereHas('customer')->get();
        return view('page.bg.bg_submissions.index', compact('recommendations'));
    }

    private function generateCustomerColumn($row) {
        $customerName = $row->recommendation->customer->name ?? '-';

        $siblingSubmissions = BgSubmission::where('bg_recommendation_id', $row->bg_recommendation_id)
                                ->where('created_at', $row->created_at)
                                ->orderBy('id', 'asc')->pluck('id')->toArray();

        $myIndex = array_search($row->id, $siblingSubmissions);

        $candidateBgs = BankGaransi::where('customer_id', $row->recommendation->customer_id)
                            ->where('created_at', $row->created_at)
                            ->orderBy('id', 'asc')->with('details')->get();

        $bg = isset($candidateBgs[$myIndex]) ? $candidateBgs[$myIndex] : $candidateBgs->first();

        $bgNumber = $bg ? $bg->bg_number : 'No BG Ref';
        $bankName = $bg && $bg->details->first() ? $bg->details->first()->bank_name : '-';
        $nominal  = $bg ? number_format($bg->bg_nominal, 0, ',', '.') : '0';

        // Tampilan sedikit berbeda untuk history vs active
        $badgeClass = in_array($row->status, ['completed', 'approved'])
                        ? 'bg-success bg-opacity-10 text-success border-success'
                        : 'bg-light text-primary border-primary-subtle';

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
        return response()->json(BgSubmission::with('recommendation')->findOrFail($id));
    }

    public function getEditData($id)
    {
        $submission = BgSubmission::with(['recommendation.customer'])->findOrFail($id);
        $rec = $submission->recommendation;
        $customer = $rec->customer;
        $metadata = json_decode($rec->notes, true) ?? [];
        $targetBg = null;

        if (isset($metadata['action']) && $metadata['action'] === 'existing' && !empty($metadata['target_bg_id'])) {
            $targetBg = BankGaransi::where('id', $metadata['target_bg_id'])
                        ->with('details')
                        ->first();
        }
        else {
            $siblingSubmissions = BgSubmission::where('bg_recommendation_id', $rec->id)
                                    ->where('created_at', $submission->created_at)
                                    ->orderBy('id', 'asc')
                                    ->pluck('id')
                                    ->toArray();

            $myIndex = array_search($submission->id, $siblingSubmissions);

            $createdAt = $submission->created_at;
            $candidateBgs = BankGaransi::where('customer_id', $customer->id)
                                ->whereBetween('created_at', [$createdAt->copy()->subSeconds(5), $createdAt->copy()->addSeconds(5)])
                                ->with('details')
                                ->orderBy('id', 'asc')
                                ->get();

            $targetBg = isset($candidateBgs[$myIndex]) ? $candidateBgs[$myIndex] : null;
        }

        if (!$targetBg) {
            $targetBg = BankGaransi::where('customer_id', $customer->id)
                ->where('status', 'draft')
                ->latest()
                ->with('details')
                ->first();
        }

        if (!$targetBg) {
             return response()->json(['success' => false, 'message' => 'Data Bank Garansi tidak ditemukan (Timestamp mismatch & No ID).']);
        }

        $totalBgDiserahkan = $targetBg->bg_nominal;
        $specificDetails = [];
        foreach($targetBg->details as $detail) {
            $detail->parent_bg_id = $targetBg->id;
            $specificDetails[] = $detail;
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

        $data = [
            'submission_id' => $submission->id,
            'bg_id' => $targetBg->id,
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

                // 2. Update Rincian BG & Details
                if(isset($request->details)) {
                    foreach ($request->details as $detailId => $val) {
                        $detailObj = BgDetail::findOrFail($detailId);

                        $detailObj->update([
                            'bank_name' => $val['bank_name'],
                            'branch_name' => $val['branch_name'],
                            'nominal' => $val['nominal'],
                        ]);

                        // Update Nominal di Parent BG juga agar sinkron
                        $parentBg = BankGaransi::find($detailObj->bank_garansi_id);
                        if ($parentBg) {
                            $parentBg->update([
                                'bg_nominal' => $val['nominal']
                            ]);
                        }
                    }
                }

                $targetBgCorrection = BankGaransi::whereHas('details', function($q) use($request) {
                    $q->whereIn('id', array_keys($request->details ?? []));
                })->first();

                if($targetBgCorrection) {
                     $this->addToBgHistory($submission, $targetBgCorrection, 'Correction via Submission Edit');
                }

                // 3. Buat Snapshot Lampiran D (Versi Koreksi)
                $lampiranD = LampiranD::firstOrCreate(
                    ['bg_submission_id' => $submission->id],
                    ['version_latest' => 0, 'created_by' => auth()->id()]
                );

                $snapshotData = [
                    'nama_distributor' => $request->nama_distributor,
                    'kota' => $request->kota,
                    'wilayah_kerja' => $request->wilayah_kerja,
                    'periode' => $request->periode, // String dari frontend
                    'rata_rata_penjualan' => $request->rata_rata_penjualan,
                    'syarat_pembayaran' => $request->syarat_pembayaran,
                    'lead_time' => $request->lead_time,
                    'faktor_fluktuasi' => $request->faktor_fluktuasi,
                    'limit_kredit' => $request->limit_kredit,
                    'nilai_bg_ditetapkan' => $request->nilai_bg_ditetapkan,
                    'nilai_bg_diserahkan' => $request->nilai_bg_diserahkan, // Ini nominal total
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

                // 4. Trigger Approval Workflow (Ke Finance)
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

                $metadata = json_decode($rec->notes, true) ?? [];
                $bgs = collect();

                $targetBgToUpdate = null;
                $allBatchBgs = collect();

                if (isset($metadata['action']) && $metadata['action'] === 'existing' && !empty($metadata['target_bg_id'])) {
                    $targetBgToUpdate = BankGaransi::where('id', $metadata['target_bg_id'])->with('details')->first();
                    if($targetBgToUpdate) {
                        $allBatchBgs->push($targetBgToUpdate);
                    }
                }
                else {
                    $createdAt = $submission->created_at;
                    $allBatchBgs = BankGaransi::where('customer_id', $customer->id)
                            ->whereBetween('created_at', [$createdAt->copy()->subSeconds(5), $createdAt->copy()->addSeconds(5)])
                            ->with('details')
                            ->orderBy('id', 'asc')
                            ->get();

                    $siblingSubmissions = BgSubmission::where('bg_recommendation_id', $rec->id)
                                            ->where('created_at', $submission->created_at)
                                            ->orderBy('id', 'asc')
                                            ->pluck('id')
                                            ->toArray();

                    $myIndex = array_search($submission->id, $siblingSubmissions);

                    if ($myIndex !== false && isset($allBatchBgs[$myIndex])) {
                        $targetBgToUpdate = $allBatchBgs[$myIndex];
                    } else {
                        $targetBgToUpdate = $allBatchBgs->first();
                    }
                }

                if ($allBatchBgs->isEmpty() || !$targetBgToUpdate) {
                    throw new \Exception("Data Bank Garansi tidak ditemukan. Cek Metadata/Timestamp.");
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
                    'remarks'       => 'Direct Approved by Admin (Nominal Updated)'
                ]);

                $lampiranD->update([
                    'version_latest'    => $nextVersion,
                    'active_version_id' => $newVersion->id
                ]);

                if($targetBgToUpdate->status == 'draft' || $targetBgToUpdate->status == 'submitted') {
                    $targetBgToUpdate->update([
                        'status'      => 'approved',
                        'issued_date' => now(),
                        'exp_date'    => now()->addYear(),
                    ]);

                    $this->addToBgHistory($submission, $targetBgToUpdate);
                }

                $submission->update([
                    'status' => 'completed',
                    'token'  => Str::random(60)
                ]);

                $pendingSiblings = BgSubmission::where('bg_recommendation_id', $submission->bg_recommendation_id)
                                    ->where('status', '!=', 'completed')
                                    ->where('status', '!=', 'approved')
                                    ->count();

                if ($pendingSiblings == 0 && $submission->recommendation) {
                    $submission->recommendation->update(['status' => 'approved']);
                }

                $this->sendCompletionEmails($submission);

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Dokumen disetujui. Lampiran D diterbitkan dengan nominal terbaru.']);

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
            'created_by'        => auth()->id() ?? null
        ]);
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
