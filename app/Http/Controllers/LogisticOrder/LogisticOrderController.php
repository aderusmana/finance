<?php

namespace App\Http\Controllers\LogisticOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Distributor;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerShipTo;
use App\Models\Customer\DistributorCustomer;
use App\Models\Customer\LogisticOrder;
use App\Models\Customer\LogisticOrderItem;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class LogisticOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = LogisticOrder::with(['distributor', 'customer', 'customerShipTo'])->select('logistic_orders.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('logistic_order_no', function($row) {
                    // Format otomatis ID menjadi LO-0001, dst saat ditampilkan
                    return 'LO-' . str_pad($row->id, 4, '0', STR_PAD_LEFT);
                })
                ->addColumn('distributor_name', function($row) {
                    return $row->distributor->name ?? '-';
                })
                ->addColumn('customer_name', function($row) {
                    return $row->customer->name ?? '-';
                })
                ->addColumn('ship_to', function($row) {
                    return $row->customerShipTo->ship_to_name ?? '-';
                })
                ->addColumn('status_badge', function($row) {
                    if($row->status == 'Pending') return '<span class="badge bg-warning text-dark">Pending</span>';
                    return '<span class="badge bg-success">'.$row->status.'</span>';
                })
                ->addColumn('action', function($row){
                    return '<button class="btn btn-sm btn-info text-white"><i class="ph-bold ph-eye"></i> Detail</button>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        // Tampilkan Customer pertama kali
        $customers = Customer::orderBy('name', 'asc')->get();

        return view('page.logistic_order.index', compact('customers'));
    }

    /**
     * API 1: Saat Customer dipilih -> Tarik Distributor, Ship To, dan Items
     */
    public function getCustomerDependencies($customerId)
    {
        $distributors = DistributorCustomer::with('distributor')
            ->where('customer_id', $customerId)
            ->get()
            ->map(function($item) { return $item->distributor; })
            ->filter()
            ->unique('id');

        $shipToLocations = CustomerShipTo::with('user')->where('customer_id', $customerId)->get();

        $customer = Customer::with('items')->find($customerId);

        return response()->json([
            'distributors' => $distributors->values(),
            'ship_to_list' => $shipToLocations,
            'items'        => $customer ? $customer->items : []
        ]);
    }

    /**
     * API 2: Saat Distributor dipilih -> Tarik Harga Logistic Fee
     */
    public function getLogisticFee($distributorId, $customerId)
    {
        $logisticFee = DistributorCustomer::where('distributor_id', $distributorId)
                        ->where('customer_id', $customerId)
                        ->first();

        return response()->json([
            'logistic_fee' => $logisticFee ? ($logisticFee->status === 'Pending' ? $logisticFee->proposed_fee : $logisticFee->logistic_fee) : 0,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'         => 'required',
            'distributor_id'      => 'required',
            'customer_ship_to_id' => 'required',
            'delivery_date'       => 'required|date',
            'items'               => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan Header
            $order = LogisticOrder::create([
                'distributor_id'      => $request->distributor_id,
                'customer_id'         => $request->customer_id,
                'customer_ship_to_id' => $request->customer_ship_to_id,
                'logistic_order_no'   => 0, // Akan diupdate di bawah
                'delivery_date'       => $request->delivery_date,
                'delivery_to'         => $request->delivery_to, // Hidden field
                'period'              => $request->period,      // Hidden field
                'status'              => 'Pending',
                'route_to'            => 'Atasan Terkait',
            ]);

            // Set nomor LO menggunakan ID integer
            $order->update(['logistic_order_no' => $order->id]);

            // 2. Simpan Item
            foreach ($request->items as $item) {
                if(empty($item['qty']) || $item['qty'] <= 0) continue;

                LogisticOrderItem::create([
                    'logistic_order_id' => $order->id,
                    'ship_to_code'      => $request->ship_to_code_header,
                    'order_item_code'   => $item['item_code'] ?? '-',
                    'order_item_name'   => $item['item_name'],
                    'order_quantity'    => $item['qty'],
                    'order_amount'      => str_replace(['Rp', '.', ' '], '', $item['amount']),
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Logistic Order (LO-'.str_pad($order->id, 4, '0', STR_PAD_LEFT).') berhasil diajukan untuk persetujuan!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }
}
