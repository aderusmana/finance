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
                ->addColumn('customer_name', function($row) {
                    return $row->customer->name ?? '-';
                })
                ->addColumn('customer_sort_name', function($row) {
                    return $row->customer->sort_name ?? '-';
                })
                ->addColumn('user_name', function($row) {
                    return $row->user->name ?? '-';
                })
                ->filterColumn('customer_code', function($query, $keyword) {
                    $query->whereHas('customer', function($q) use ($keyword) {
                        $q->where('code', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('customer_code', function($query, $order) {
                    $query->orderBy(
                        Customer::select('code')->whereColumn('customers.id', 'customer_ship_toes.customer_id'),
                        $order
                    );
                })
                ->filterColumn('customer_name', function($query, $keyword) {
                    $query->whereHas('customer', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('customer_name', function($query, $order) {
                    $query->orderBy(
                        Customer::select('name')->whereColumn('customers.id', 'customer_ship_toes.customer_id'),
                        $order
                    );
                })
                ->filterColumn('customer_sort_name', function($query, $keyword) {
                    $query->whereHas('customer', function($q) use ($keyword) {
                        $q->where('sort_name', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('customer_sort_name', function($query, $order) {
                    $query->orderBy(
                        Customer::select('sort_name')->whereColumn('customers.id', 'customer_ship_toes.customer_id'),
                        $order
                    );
                })
                ->filterColumn('user_name', function($query, $keyword) {
                    $query->whereHas('user', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('user_name', function($query, $order) {
                    $query->orderBy(
                        User::select('name')->whereColumn('users.id', 'customer_ship_toes.user_id'),
                        $order
                    );
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="d-flex flex-row gap-2 flex-wrap">';
                    $btn .= '<button class="btn btn-sm btn-info text-white btn-detail" data-id="'.$row->id.'"><i class="ph-bold ph-eye"></i> Detail</button>';
                    $btn .= '<button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'"><i class="ph-bold ph-pencil"></i> Edit</button>';
                    $btn .= '<button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'"><i class="ph-bold ph-trash"></i> Delete</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = Customer::all();
        $users = User::role('sales-ka')->orderBy('name', 'asc')->get();

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
        $shipTo = CustomerShipTo::with(['customer', 'user'])->findOrFail($id);
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
