<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BG\Tax;
use Illuminate\Http\Request;
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
                    // Tampilkan sebagai persen di table (misal 0.11 jadi 11%)
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

        // Konversi Input Persen (misal 11) ke Desimal (0.11) agar sesuai logic sebelumnya
        $decimalValue = $request->value / 100;

        Tax::create([
            'name' => $request->name,
            'value' => $decimalValue
        ]);

        return response()->json(['success' => true, 'message' => 'Tax Saved']);
    }

    public function show($id)
    {
        $tax = Tax::findOrFail($id);
        // Kembalikan ke format persen untuk diedit (0.11 -> 11)
        $tax->value = $tax->value * 100;
        return response()->json($tax);
    }

    public function update(Request $request, $id)
    {
        $tax = Tax::findOrFail($id);
        $decimalValue = $request->value / 100;

        $tax->update([
            'name' => $request->name,
            'value' => $decimalValue
        ]);
        return response()->json(['success' => true, 'message' => 'Tax Updated']);
    }

    public function destroy($id)
    {
        Tax::destroy($id);
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
