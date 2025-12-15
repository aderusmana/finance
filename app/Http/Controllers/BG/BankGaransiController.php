<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class BankGaransiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BankGaransi::with(['customer', 'details'])->select('bank_garansi.*');

            if ($request->has('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }
            if ($request->has('bg_type') && $request->bg_type != 'all') {
                $query->where('bg_type', $request->bg_type);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('issued_date', function($row){
                    return $row->issued_date ? $row->issued_date->format('d M Y') : '-';
                })
                ->editColumn('exp_date', function($row){
                    return $row->exp_date ? $row->exp_date->format('d M Y') : '-';
                })
                ->editColumn('bg_nominal', function($row){
                    return 'Rp ' . number_format($row->bg_nominal, 0, ',', '.');
                })
                ->addColumn('customer_name', function($row){
                    return $row->customer ? $row->customer->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    // Tombol VIEW (Mata) -> Masuk ke halaman detail
                    $viewBtn = '<a href="' . route('bg-list.show', $row->id) . '" class="btn btn-sm btn-info text-white" title="View Detail"><i class="ph-bold ph-eye"></i></a>';

                    // Tombol EDIT (Pensil) -> Buka Modal (logic JS tetap jalan)
                    $editBtn = '<button type="button" class="btn btn-sm btn-warning btn-edit-bg text-white" data-id="' . $row->id . '" title="Edit"><i class="ph-bold ph-pencil-simple"></i></button>';

                    // Tombol DELETE
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete-bg text-white" data-id="' . $row->id . '" title="Delete"><i class="ph-bold ph-trash"></i></button>';

                    return '<div class="d-flex gap-2 justify-content-center">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = Customer::select('id', 'name', 'code')->orderBy('name')->get();

        // Statistik (tetap sama)
        $stats = [
            'total' => BankGaransi::count(),
            'active' => BankGaransi::where('status', 'approved')->count(),
            'draft' => BankGaransi::where('status', 'draft')->count(),
            'expiring' => BankGaransi::where('exp_date', '<', now()->addMonth())->where('status', 'approved')->count(),
        ];

        return view('page.bg.bg_list.index', compact('customers', 'stats'));
    }

    public function show($id)
    {
        // Jika request AJAX (dari tombol Edit), kembalikan JSON
        if (request()->wantsJson()) {
            $bg = BankGaransi::with(['details', 'customer'])->findOrFail($id);
            return response()->json($bg);
        }

        // Jika request biasa (klik tombol View), tampilkan Halaman Detail
        // Load relasi histories.user agar nama user muncul di tab history
        $bg = BankGaransi::with(['customer', 'details', 'histories.user', 'creator'])->findOrFail($id);

        return view('page.bg.bg_list.show', compact('bg'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'bg_number' => 'required|unique:bank_garansi,bg_number',
            'bg_nominal' => 'required|numeric|min:0',
            'bg_type' => 'required',
            'status' => 'required',
            'details' => 'array',
            'details.*.bank_name' => 'required_with:details.*.nominal',
            'details.*.nominal' => 'required_with:details.*.bank_name|numeric|min:0',
        ]);

        $totalDetailInfo = 0;
        if ($request->has('details')) {
            foreach ($request->details as $d) {
                $totalDetailInfo += isset($d['nominal']) ? (float)$d['nominal'] : 0;
            }
        }

        // Cek selisih (gunakan epsilon untuk floating point comparison)
        if (abs((float)$request->bg_nominal - $totalDetailInfo) > 1.0) { // Toleransi Rp 1 perak
            return response()->json([
                'success' => false,
                'message' => 'Validation Error: Total Nominal Header (Rp '.number_format($request->bg_nominal).') tidak sama dengan total rincian bank (Rp '.number_format($totalDetailInfo).').'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $request->except(['details', 'id', '_method', '_token', 'deleted_detail_ids']);
            $data['created_by'] = auth()->id();

            // Observer 'created' akan jalan disini otomatis menyimpan log history
            $bg = BankGaransi::create($data);

            // Simpan Details
            if ($request->has('details')) {
                foreach ($request->details as $detail) {
                    if(!empty($detail['bank_name']) || !empty($detail['nominal'])) {
                        $bg->details()->create($detail);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Bank Garansi created successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'customer_id' => 'required',
            // Unique check harus mengecualikan ID saat ini (ignore $id)
            'bg_number' => 'required|unique:bank_garansi,bg_number,' . $id,
            'bg_nominal' => 'required|numeric|min:0',
            'bg_type' => 'required',
            'status' => 'required',
            // Validasi array details
            'details' => 'array',
            'details.*.bank_name' => 'required_with:details.*.nominal',
            'details.*.nominal' => 'required_with:details.*.bank_name|numeric|min:0',
        ]);

        // 2. Validasi Konsistensi Data (Header vs Total Detail)
        // Kita hitung total dari data details yang dikirim dari form (Front-end mengirim semua active rows)
        $totalDetailInfo = 0;
        if ($request->has('details')) {
            foreach ($request->details as $d) {
                // Skip baris kosong jika ada yang lolos
                if (empty($d['bank_name']) && empty($d['nominal'])) continue;

                $totalDetailInfo += isset($d['nominal']) ? (float)$d['nominal'] : 0;
            }
        }

        // Cek selisih (gunakan toleransi 1 rupiah untuk keamanan floating point)
        if (abs((float)$request->bg_nominal - $totalDetailInfo) > 1.0) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error: Total Nominal Header (Rp '.number_format($request->bg_nominal).') tidak sama dengan total rincian bank (Rp '.number_format($totalDetailInfo).'). Silakan cek kembali inputan Anda.'
            ], 422);
        }

        // 3. Mulai Transaksi Database
        DB::beginTransaction();
        try {
            $bg = BankGaransi::findOrFail($id);

            // Ambil data request kecuali data detail & token
            $data = $request->except(['details', 'id', '_method', '_token', 'deleted_detail_ids']);

            // Update Header BG
            // Note: Observer 'updated' akan otomatis jalan di sini jika ada perubahan pada nominal/exp_date/status
            // Observer akan mencatat 'bg_histories' sesuai logic yang sudah kita buat sebelumnya
            $bg->update($data);

            // 4. Handle Detail Deletion (Menghapus baris yang dibuang user di form)
            if ($request->filled('deleted_detail_ids')) {
                $deletedIds = explode(',', $request->deleted_detail_ids);
                // Hapus detail yang ID-nya ada di list deleted
                $bg->details()->whereIn('id', $deletedIds)->delete();
            }

            // 5. Handle Detail Update/Create (Looping data dari form)
            if ($request->has('details')) {
                foreach ($request->details as $detail) {
                    // Skip row kosong
                    if(empty($detail['bank_name']) && empty($detail['nominal'])) continue;

                    if (isset($detail['id']) && $detail['id']) {
                        // Jika ID ada, berarti data lama -> Update
                        $bg->details()->where('id', $detail['id'])->update([
                            'bank_name' => $detail['bank_name'],
                            'branch_name' => $detail['branch_name'] ?? null,
                            'bank_address' => $detail['bank_address'] ?? null,
                            'contact_person' => $detail['contact_person'] ?? null,
                            'nominal' => $detail['nominal'],
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Jika ID kosong, berarti baris baru -> Create
                        // Kita gunakan method create() dari relasi details() agar bg_id otomatis terisi
                        $bg->details()->create($detail);
                    }
                }
            }

            // Commit Transaksi
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Bank Garansi updated successfully!']);

        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $bg = BankGaransi::findOrFail($id);
            $bg->delete();
            return response()->json(['success' => true, 'message' => 'Bank Garansi deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
