<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\LampiranD;
use App\Models\BG\LampiranDVersion;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LampiranDController extends Controller
{
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
                        // Tombol Lihat Detail Snapshot
                        return '<button class="btn btn-xs btn-outline-info btn-view-snapshot" data-id="'.$row->id.'">
                                    <i class="ph-bold ph-eye"></i> Data
                                </button>';
                    })
                    ->rawColumns(['version', 'action'])
                    ->make(true);
            }

            // MODE 2: DATA OVERVIEW (DEFAULT - ACTIVE DOCS)
            // Hanya ambil versi terbaru dari Lampiran D
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
                    // Tombol Edit
                    return '<button type="button" class="btn btn-sm btn-warning btn-edit-lampiran" data-id="' . $row->id . '">
                                <i class="ph-bold ph-pencil-simple text-white"></i> Edit
                            </button>';
                })
                ->rawColumns(['version', 'action'])
                ->make(true);
        }

        return view('page.bg.lampiran_d.index');
    }

    // --- METHOD LAIN TETAP SAMA (STORE, UPDATE, SHOW, DLL) ---
    
    public function show($id)
    {
        // Digunakan untuk modal edit (ambil data current)
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
        // LOGIC SAMA PERSIS SEPERTI SEBELUMNYA (VERSIONING)
        DB::beginTransaction();
        try {
            $lampiranD = LampiranD::with('submission.recommendation.customer')->findOrFail($id);
            $rec = $lampiranD->submission->recommendation;
            $customer = $rec->customer;

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
                'remarks'       => $request->remarks ?? 'Edited by User',
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

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data updated to Version ' . $nextVersion]);

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