<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgLimitRule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BgLimitRuleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BgLimitRule::query();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-bg-limit-rule" data-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('bg-limit-rules.destroy', $row->id) . '" method="POST" style="display:inline;">'
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

        return view('page.bg.bg_limit_rules.index');
    }

    public function show(BgLimitRule $bgLimitRule)
    {
        return $bgLimitRule;
    }

    public function store(Request $request)
    {
        $rule = BgLimitRule::create($request->all());
        return response()->json(['success' => true, 'message' => 'Rule created successfully!', 'data' => $rule], 201);
    }

    public function update(Request $request, BgLimitRule $bgLimitRule)
    {
        $bgLimitRule->update($request->all());
        return response()->json(['success' => true, 'message' => 'Rule updated successfully!', 'data' => $bgLimitRule]);
    }

    public function destroy(BgLimitRule $bgLimitRule)
    {
        $bgLimitRule->delete();
        return response()->json(['success' => true, 'message' => 'Rule deleted successfully!']);
    }
}
