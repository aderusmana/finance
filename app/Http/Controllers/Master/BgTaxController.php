<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BG\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BgTaxController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Tax::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('value', function($row){
                    return ($row->value * 100) . '%';
                })
                ->addColumn('action', function($row){
                    return '<button class="btn btn-sm btn-warning btn-edit text-white" data-id="'.$row->id.'"><i class="ph-bold ph-pencil-simple"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('page.master.bg_tax.index');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'value' => 'required|numeric']);
        $decimalValue = $request->value / 100;

        $tax = Tax::create([
            'name' => $request->name,
            'value' => $decimalValue
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($tax)
            ->useLog('master_bg_tax')
            ->event('create')
            ->withProperties(['attributes' => $tax->toArray()])
            ->log("Created Tax: {$request->name} ({$request->value}%)");

        return response()->json(['success' => true, 'message' => 'Tax Saved']);
    }

    public function update(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);
        $oldData = $tax->getOriginal();

        $decimalValue = $request->value / 100;

        $tax->update([
            'name' => $request->name,
            'value' => $decimalValue
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($tax)
            ->useLog('master_bg_tax')
            ->event('update')
            ->withProperties([
                'old' => $oldData,
                'attributes' => $tax->getChanges()
            ])
            ->log("Updated Tax: {$request->name}");

        return response()->json(['success' => true, 'message' => 'Tax Updated']);
    }

    public function show($id)
    {
        $tax = Tax::findOrFail($id);
        $tax->value = $tax->value * 100;
        return response()->json($tax);
    }

    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);
        $oldData = $tax->toArray();

        Tax::destroy($id);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($tax)
            ->useLog('master_bg_tax')
            ->event('delete')
            ->withProperties(['attributes' => $oldData])
            ->log("Deleted Tax: {$oldData['name']}");

        return response()->json(['success' => true, 'message' => 'Data Tax Deleted']);
    }
}
