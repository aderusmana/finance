<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Distributor;
use Yajra\DataTables\Facades\DataTables;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Distributor::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '
                        <button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'"><i class="ph-bold ph-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'"><i class="ph-bold ph-trash"></i> Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('page.master.distributor.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:distributors,code',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:distributors,email'
        ]);

        Distributor::create($request->all());

        return response()->json(['success' => true, 'message' => 'Distributor berhasil ditambahkan!']);
    }

    public function show($id)
    {
        $distributor = Distributor::findOrFail($id);
        return response()->json($distributor);
    }

    public function update(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:distributors,code,'.$id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:distributors,email,'.$id
        ]);

        $distributor->update($request->all());

        return response()->json(['success' => true, 'message' => 'Distributor berhasil diubah!']);
    }

    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->delete();

        return response()->json(['success' => true, 'message' => 'Distributor berhasil dihapus!']);
    }

    public function getCustomersByDistributor($distributor_id)
    {
        // Mengambil customer yang sudah punya baris di tabel pivot distributor_customers
        $distributor = Distributor::with('customers')->findOrFail($distributor_id);

        return response()->json($distributor->customers);
    }
}
