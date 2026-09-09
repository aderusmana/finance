<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Distributor;
use App\Models\Customer\Customer;
use Yajra\DataTables\Facades\DataTables;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Distributor::with('customer')->select('distributors.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function($row) {
                    $badge = '';
                    if ($row->customer_id && $row->customer) {
                        $badge = ' <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 ms-1" style="font-size: 0.72rem;" title="Terhubung dengan Master Customer"><i class="ph-bold ph-link-simple"></i> Customer</span>';
                    }
                    return '<span>' . e($row->name) . '</span>' . $badge;
                })
                ->addColumn('action', function($row){
                    return '
                        <button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'"><i class="ph-bold ph-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'"><i class="ph-bold ph-trash"></i> Hapus</button>
                    ';
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        $customers = Customer::whereNotNull('code')
            ->where('code', '!=', '')
            ->orderByRaw("CASE WHEN code LIKE 'ID%' OR code LIKE 'IE%' THEN 0 ELSE 1 END")
            ->orderBy('code', 'asc')
            ->get(['id', 'code', 'name', 'email']);

        return view('page.master.distributor.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'code'        => 'required|unique:distributors,code',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:distributors,email'
        ]);

        Distributor::create($request->all());

        return response()->json(['success' => true, 'message' => 'Distributor berhasil ditambahkan!']);
    }

    public function show($id)
    {
        $distributor = Distributor::with('customer')->findOrFail($id);
        return response()->json($distributor);
    }

    public function update(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);

        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'code'        => 'required|unique:distributors,code,'.$id,
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:distributors,email,'.$id
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
