<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BgHistoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Load relasi ke BankGaransi -> Customer, dan User pembuat
            $query = BgHistory::with(['bankGaransi.customer', 'creator'])
                        ->latest('created_at'); // Urutkan dari yang terbaru

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', function($row){
                    return $row->bankGaransi->customer->name ?? '-';
                })
                ->addColumn('bg_number', function($row){
                    return $row->bankGaransi->bg_number ?? '-';
                })
                ->addColumn('nominal_change', function($row){
                    // Format: Rp 1.000.000 -> Rp 2.000.000
                    $prev = number_format($row->previous_nominal, 0, ',', '.');
                    $new  = number_format($row->new_nominal, 0, ',', '.');

                    // Highlight jika ada perubahan
                    if ($row->previous_nominal != $row->new_nominal) {
                        return '<div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Rp '.$prev.'</span>
                                    <i class="ph-bold ph-arrow-right text-primary" style="font-size:10px;"></i>
                                    <span class="fw-bold text-dark">Rp '.$new.'</span>
                                </div>';
                    }
                    return '<span class="fw-bold">Rp '.$new.'</span>';
                })
                ->addColumn('date_change', function($row){
                    if (!$row->previous_exp_date && !$row->new_exp_date) return '-';

                    $prevDate = $row->previous_exp_date ? Carbon::parse($row->previous_exp_date)->format('d M Y') : '-';
                    $newDate  = $row->new_exp_date ? Carbon::parse($row->new_exp_date)->format('d M Y') : '-';

                    if ($prevDate != $newDate) {
                        return '<div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">'.$prevDate.'</span>
                                    <i class="ph-bold ph-arrow-right text-primary" style="font-size:10px;"></i>
                                    <span class="fw-bold text-dark">'.$newDate.'</span>
                                </div>';
                    }
                    return $newDate;
                })
                ->editColumn('remarks', function($row){
                    return '<span class="text-muted f-s-12">'.Str::limit($row->remarks, 50).'</span>';
                })
                ->addColumn('user', function($row){
                    return $row->creator->name ?? 'System';
                })
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) {
                    // Tombol Delete (Hanya untuk Super Admin jika perlu)
                    return '
                        <form action="' . route('bg-histories.destroy', $row->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure?\')">'
                        . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-xs btn-danger" title="Delete Log">
                                <i class="ph-bold ph-trash text-white"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['nominal_change', 'date_change', 'remarks', 'action'])
                ->make(true);
        }

        return view('page.bg.bg_histories.index');
    }

    public function show(BgHistory $bgHistory)
    {
        return $bgHistory;
    }

    public function store(Request $request)
    {
        $h = BgHistory::create($request->all());
        return response()->json(['success' => true, 'message' => 'History created successfully!', 'data' => $h], 201);
    }

    public function update(Request $request, BgHistory $bgHistory)
    {
        $bgHistory->update($request->all());
        return response()->json(['success' => true, 'message' => 'History updated successfully!', 'data' => $bgHistory]);
    }

    public function destroy(BgHistory $bgHistory)
    {
        $bgHistory->delete();
        return response()->json(['success' => true, 'message' => 'History deleted successfully!']);
    }
}
