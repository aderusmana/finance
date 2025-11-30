<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BgHistoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BgHistory::with('bankGaransi');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-bg-history" data-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('bg-histories.destroy', $row->id) . '" method="POST" style="display:inline;">'
                            . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt text-white"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
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
