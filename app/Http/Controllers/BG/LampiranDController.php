<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\LampiranD;
use App\Models\BG\LampiranDVersion;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Models\Master\ApprovalLog;
use App\Models\Master\ApprovalPath;
use App\Models\User;
use App\Traits\ApprovalTrait;
use App\Jobs\ProcessFinanceApprovalEmail;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class LampiranDController extends Controller
{
    use ApprovalTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // MODE 1: DATA VERSIONS (HISTORY GLOBAL)
            if ($request->mode == 'versions') {
                $query = LampiranDVersion::with(['lampiranD.submission.recommendation.customer', 'generator'])
                            ->orderBy('generated_at', 'desc');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('customer', function ($row) {
                        return $row->lampiranD->submission->recommendation->customer->name ?? '-';
                    })
                    ->addColumn('form_code', function ($row) {
                        return $row->lampiranD->submission->form_code ?? '-';
                    })
                    ->addColumn('version', function ($row) {
                        return '<span class="badge bg-secondary">v' . $row->version_no . '</span>';
                    })
                    ->addColumn('modified_by', function ($row) {
                        return $row->generator->name ?? 'System';
                    })
                    ->addColumn('date', function ($row) {
                        return $row->generated_at->format('d M Y H:i');
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="action-btn-group"><button class="btn btn-info action-btn-hover btn-view-snapshot" data-id="'.$row->id.'" data-tooltip="View Data">
                                    <i class="ph-bold ph-eye"></i>
                                </button></div>';
                    })
                    ->rawColumns(['version', 'action'])
                    ->make(true);
            }

            // MODE 2: DATA OVERVIEW (DEFAULT - ACTIVE DOCS)
            $query = LampiranD::with(['submission.recommendation.customer']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    return $row->submission->recommendation->customer->name ?? '-';
                })
                ->addColumn('form_code', function ($row) {
                    return $row->submission->form_code ?? '-';
                })
                ->addColumn('version', function ($row) {
                    // Versi aktif saat ini
                    return '<span class="badge bg-primary">v' . $row->version_latest . '</span>';
                })
                ->addColumn('last_updated', function ($row) {
                    return $row->updated_at->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) {
                    $downloadUrl = route('bg-reports.download', ['id' => $row->bg_submission_id, 'doc_type' => 'lampiran_d']);
                    $btn = '<div class="d-flex justify-content-center gap-1">';
                    $btn .= '<a href="' . $downloadUrl . '" target="_blank" class="btn btn-sm btn-outline-danger" title="Download PDF Lampiran D">
                                <i class="ph-bold ph-file-pdf me-1"></i> PDF
                            </a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-warning btn-edit-lampiran" data-id="' . $row->id . '" title="Edit & Create New Version">
                                <i class="ph-bold ph-pencil-simple text-white me-1"></i> Edit
                            </button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['version', 'action'])
                ->make(true);
        }

        return view('page.bg.lampiran_d.index');
    }

    public function show($id)
    {
        $lampiran = LampiranD::with(['submission.recommendation.customer'])->findOrFail($id);
        $rec = $lampiran->submission->recommendation;
        $customer = $rec->customer;

        $bg = BankGaransi::where('customer_id', $customer->id)
                ->where('status', 'submitted')->latest()->first();

        $data = [
            'id' => $lampiran->id,
            'customer_name' => $customer->name,
            'customer_city' => $customer->city,
            'customer_area' => $customer->area,
            'average' => $rec->average,
            'top' => $rec->top,
            'lead_time' => $rec->lead_time,
            'inflation' => $rec->inflation,
            'credit_limit' => $rec->credit_limit_updated,
            'set_bg' => $rec->set_bg,
            'bg_nominal' => $bg ? $bg->bg_nominal : 0,
        ];

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $lampiranD = LampiranD::with('submission.recommendation.customer')->findOrFail($id);
            $submission = $lampiranD->submission;
            $rec = $submission ? $submission->recommendation : null;
            $customer = $rec ? $rec->customer : null;

            if (!$customer) {
                throw new \Exception("Data customer terkait tidak ditemukan.");
            }

            // 1. Simpan Snapshot Versi Baru
            $nextVersion = $lampiranD->version_latest + 1;

            // Siapkan data untuk snapshot (simpan inputan user)
            $dataSnapshot = $request->except(['_token', '_method', 'remarks']);

            $version = LampiranDVersion::create([
                'lampiran_d_id' => $lampiranD->id,
                'version_no'    => $nextVersion,
                'data_snapshot' => $dataSnapshot,
                'generated_by'  => Auth::id(),
                'generated_at'  => now(),
                'remarks'       => $request->remarks ?? 'Revision via Lampiran D Management',
            ]);

            // 2. Update Data Utama (Customer, Recommendation, BG)
            $customer->update([
                'name' => $request->customer_name,
                'city' => $request->customer_city,
                'area' => $request->customer_area
            ]);

            $rec->update([
                'average' => $request->average,
                'top' => $request->top,
                'lead_time' => $request->lead_time,
                'inflation' => $request->inflation,
                'credit_limit_updated' => $request->credit_limit,
                'set_bg' => $request->set_bg,
            ]);

            // Update BG Nominal
            $bg = BankGaransi::where('customer_id', $customer->id)
                    ->where('status', 'submitted')->latest()->first();
            if($bg) {
                $bg->update(['bg_nominal' => $request->bg_nominal]);
            }

            // 3. Update Pointer Version di Table LampiranD
            $lampiranD->update([
                'version_latest' => $nextVersion,
                'active_version_id' => $version->id
            ]);

            // 4. Trigger Approval Workflow ke Manager Finance
            if ($submission) {
                $requester = Auth::user();
                $submission->update(['status' => 'waiting_approval']);

                $logs = $this->generateApprovalLogs($requester, $submission->id, 'BG', 'Lampiran D');

                if ($logs->isNotEmpty()) {
                    $firstLog = ApprovalLog::where('category', 'BG')
                        ->where('related_id', $submission->id)
                        ->where('status', 'Pending')
                        ->orderBy('level', 'asc')
                        ->first();

                    if ($firstLog) {
                        ProcessFinanceApprovalEmail::dispatch($firstLog, $submission);
                    }
                }

                // Kirim notifikasi lonceng ke Manager Finance & Head Finance
                $approvers = User::role(['manager-finance', 'head-finance'])->get();
                Notification::send($approvers, new SystemNotification(
                    'Approval Lampiran D Diperlukan',
                    "Revisi Lampiran D v{$nextVersion} untuk <b>{$customer->name}</b> menunggu persetujuan Anda.",
                    route('bg-approvals.index'),
                    'ph-signature',
                    'warning'
                ));

                // Log Activity
                activity()
                    ->causedBy($requester)
                    ->performedOn($submission)
                    ->useLog('lampiran_d')
                    ->event('update_version')
                    ->withProperties([
                        'version' => $nextVersion,
                        'customer' => $customer->name,
                        'remarks' => $request->remarks ?? 'Revision via Lampiran D Management'
                    ])
                    ->log("Revisi Lampiran D v{$nextVersion} diajukan dan diteruskan ke Manager Finance.");
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data updated to Version ' . $nextVersion . ' & forwarded to Manager Finance for approval.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Helper untuk ambil detail version JSON
    public function showVersionDetail($versionId) {
        $v = LampiranDVersion::findOrFail($versionId);
        return response()->json($v);
    }
}
