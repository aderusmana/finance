<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerFillFormNotification; // Pastikan Mailable ini ada
use Carbon\Carbon;

class BgRecommendationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Nilai Persentase Kenaikan (11%) dari table Texts
        $textPercent = DB::table('texts')->where('name', 'increase_percentage')->first();
        $defaultPercent = $textPercent ? (float)$textPercent->value : 11; // Default 11 jika kosong

        // 2. Jika Request AJAX untuk Datatable LIST REKOMENDASI (History)
        if ($request->ajax() && $request->has('type') && $request->type == 'history') {
            $query = BgRecommendation::with('customer')->select('bg_recommendations.*');
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('recommended_credit_limit', function($row){
                    return 'Rp ' . number_format($row->recommended_credit_limit, 0, ',', '.');
                })
                ->addColumn('customer_name', function($row){
                    return $row->customer ? $row->customer->name : '-';
                })
                ->addColumn('status_badge', function($row){
                    // Contoh badge sederhana
                    return '<span class="badge bg-primary">'.$row->status.'</span>';
                })
                ->make(true);
        }

        // 3. Jika Request AJAX untuk Datatable EXPIRING BGs (Action Needed)
        if ($request->ajax() && $request->has('type') && $request->type == 'expiring') {
            // Logika: Ambil BG yang expired dalam rentang 60 hari ke depan
            // Atau ambil semua yang status approved dan mendekati expired
            $startDate = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->addDays(60)->format('Y-m-d');

            $query = BankGaransi::with('customer')
                ->where('status', 'approved')
                ->whereBetween('exp_date', [$startDate, $endDate])
                ->select('bank_garansi.*');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('bg_nominal', function($row){
                    return 'Rp ' . number_format($row->bg_nominal, 0, ',', '.');
                })
                ->editColumn('exp_date', function($row){
                    // Tandai merah jika < 30 hari (opsional style)
                    return Carbon::parse($row->exp_date)->format('d M Y');
                })
                ->addColumn('action', function ($row) use ($defaultPercent) {
                    // Tombol Process mengirim data customer & nominal ke Modal
                    return '<button type="button" class="btn btn-sm btn-success btn-process text-white"
                        data-id="'.$row->id.'"
                        data-customer-id="'.$row->customer_id.'"
                        data-customer-name="'.$row->customer->name.'"
                        data-nominal="'.$row->bg_nominal.'"
                        data-percent="'.$defaultPercent.'"
                        title="Buat Rekomendasi"><i class="ph-bold ph-plus"></i> Process</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = Customer::all();

        // Load view dengan data persentase untuk dilempar ke JS
        return view('page.bg.bg_recommendations.index', compact('defaultPercent', 'customers'));
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'customer_id' => 'required',
            'average' => 'required|numeric',
            // 'increase_percent' diambil dari input hidden atau db
        ]);

        DB::beginTransaction();
        try {
            // 1. Hitung Ulang (Backend Validation)
            $average = (float)$request->average;
            // Ambil persen dari DB lagi agar aman, atau dari request
            $textPercent = DB::table('texts')->where('name', 'increase_percentage')->first();
            $percent = $textPercent ? (float)$textPercent->value : 11;

            // Rumus: Average + (Average * 11%)
            $recommended = $average + ($average * ($percent / 100));

            // 2. Simpan Rekomendasi
            $rec = BgRecommendation::create([
                'customer_id' => $request->customer_id,
                'average' => $average,
                'increase_percent' => $percent,
                'recommended_credit_limit' => $recommended,
                // field lain sesuai kebutuhan (inflation, rounded, dll)
                'status' => 'sent_to_customer',
                'notes' => $request->notes,
            ]);

            // 3. Kirim Email ke Customer
            if ($rec->customer && $rec->customer->email) {
                // Pastikan Anda sudah membuat Mailable: CustomerFillFormNotification
                Mail::to($rec->customer->email)->send(new CustomerFillFormNotification($rec));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Rekomendasi berhasil dibuat & Email terkirim ke Customer!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return response()->json(BgRecommendation::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $rec = BgRecommendation::findOrFail($id);
        $rec->update($request->all());
        return response()->json(['success' => true, 'message' => 'Recommendation updated!']);
    }

    public function destroy($id)
    {
        BgRecommendation::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully']);
    }
}
