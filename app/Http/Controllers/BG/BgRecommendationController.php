<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgRecommendation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BgRecommendationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BgRecommendation::with('customer');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-bg-recommendation" data-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('bg-recommendations.destroy', $row->id) . '" method="POST" style="display:inline;">'
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

        return view('page.bg.bg_recommendations.index');
    }

    public function show(BgRecommendation $bgRecommendation)
    {
        return $bgRecommendation;
    }

    public function store(Request $request)
    {
        $rec = BgRecommendation::create($request->all());
        return response()->json(['success' => true, 'message' => 'Recommendation created successfully!', 'data' => $rec], 201);
    }

    public function update(Request $request, BgRecommendation $bgRecommendation)
    {
        $bgRecommendation->update($request->all());
        return response()->json(['success' => true, 'message' => 'Recommendation updated successfully!', 'data' => $bgRecommendation]);
    }

    public function destroy(BgRecommendation $bgRecommendation)
    {
        $bgRecommendation->delete();
        return response()->json(['success' => true, 'message' => 'Recommendation deleted successfully!']);
    }
}
