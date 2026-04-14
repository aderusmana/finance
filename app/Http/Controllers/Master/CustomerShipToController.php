<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\CustomerShipTo;
use App\Models\Customer\Customer;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class CustomerShipToController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CustomerShipTo::with(['customer', 'user'])->select('customer_ship_toes.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer_code', function($row) {
                    return $row->customer->code ?? '-';
                })
                ->addColumn('user_name', function($row) {
                    return $row->user->name ?? '-';
                })
                ->addColumn('action', function($row){
                    $btn = '<button class="btn btn-sm btn-primary btn-edit me-1" data-id="'.$row->id.'"><i class="ph-bold ph-pencil"></i> Edit</button>';
                    $btn .= '<button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'"><i class="ph-bold ph-trash"></i> Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = Customer::all();
        $users = User::role('staff-sales')->orderBy('name', 'asc')->get();

        return view('page.master.ship-to.index', compact('customers', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'user_id'           => 'required|exists:users,id',
            'ship_to_code'      => 'required|string|max:255',
            'ship_to_name'      => 'required|string|max:255',
            'ship_to_address_1' => 'required|string|max:255',
            'ship_to_address_2' => 'nullable|string|max:255',
            'ship_to_address_3' => 'nullable|string|max:255',
            'ship_to_city'      => 'required|string|max:255',
        ]);

        CustomerShipTo::create($request->all());

        return response()->json(['success' => true, 'message' => 'Data Ship To berhasil disimpan.']);
    }

    public function show($id)
    {
        $shipTo = CustomerShipTo::findOrFail($id);
        return response()->json($shipTo);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'user_id'           => 'required|exists:users,id',
            'ship_to_code'      => 'required|string|max:255',
            'ship_to_name'      => 'required|string|max:255',
            'ship_to_address_1' => 'required|string|max:255',
            'ship_to_address_2' => 'nullable|string|max:255',
            'ship_to_address_3' => 'nullable|string|max:255',
            'ship_to_city'      => 'required|string|max:255',
        ]);

        $shipTo = CustomerShipTo::findOrFail($id);
        $shipTo->update($request->all());

        return response()->json(['success' => true, 'message' => 'Data Ship To berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $shipTo = CustomerShipTo::findOrFail($id);
        $shipTo->delete();

        return response()->json(['success' => true, 'message' => 'Data Ship To berhasil dihapus.']);
    }
}
