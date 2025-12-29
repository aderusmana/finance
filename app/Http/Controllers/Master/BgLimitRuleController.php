<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BG\BgLimitRule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BgLimitRuleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BgLimitRule::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('range', function($row){
                    return $row->min_year . ' - ' . $row->max_year . ' Tahun';
                })
                ->editColumn('percentage', function($row){
                    return $row->percentage . '%';
                })
                ->orderColumn('range', function ($query, $order) {
                    $query->orderBy('min_year', $order);
                })
                ->addColumn('action', function($row){
                    $btn = '<button class="btn btn-sm btn-warning btn-edit text-white me-1" data-id="'.$row->id.'"><i class="ph-bold ph-pencil-simple"></i></button>';
                    $btn .= '<button class="btn btn-sm btn-danger btn-delete text-white" data-id="'.$row->id.'"><i class="ph-bold ph-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('page.master.bg_limit_rule.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'min_year' => 'required|numeric',
            'max_year' => 'required|numeric',
            'percentage' => 'required|numeric',
            'description' => 'required'
        ]);

        BgLimitRule::create($request->all());
        return response()->json(['success' => true, 'message' => 'Rule Saved']);
    }

    public function show($id)
    {
        return response()->json(BgLimitRule::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $rule = BgLimitRule::findOrFail($id);
        $rule->update($request->all());
        return response()->json(['success' => true, 'message' => 'Rule Updated']);
    }

    public function destroy($id)
    {
        BgLimitRule::destroy($id);
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
