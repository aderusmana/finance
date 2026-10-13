<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\CustomerItem;
use App\Models\Customer\Customer;
use Yajra\DataTables\Facades\DataTables;

class CustomerItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::whereHas('items')->with('items')->select('customers.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer_code', function($row) {
                    return $row->code ?? '-';
                })
                ->addColumn('customer_name', function($row) {
                    return $row->name ?? '-';
                })
                ->addColumn('customer_sort_name', function($row) {
                    return $row->sort_name ?? '-';
                })
                ->addColumn('items_preview', function($row) {
                    $items = $row->items;
                    if ($items->isEmpty()) {
                        return '<span class="text-muted fst-italic">Belum ada item</span>';
                    }
                    $html = '<div class="d-flex flex-wrap gap-1">';
                    foreach ($items as $item) {
                        $codeBadge = $item->item_code ? '<span class="fw-bold text-dark">[' . e($item->item_code) . ']</span> ' : '';
                        $qtyText = ($item->quantity !== null && $item->quantity !== '') ? ' <span class="text-muted">(' . number_format($item->quantity, 0, ',', '.') . ' pcs)</span>' : '';
                        $html .= '<span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.82rem; font-weight: 500;">';
                        $html .= '<i class="ph-bold ph-package text-primary me-1"></i>' . $codeBadge . e($item->item_name) . $qtyText;
                        $html .= '</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('total_items', function($row) {
                    $count = $row->items->count();
                    return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 fw-bold">' . $count . ' Item' . ($count > 1 ? 's' : '') . '</span>';
                })
                ->filterColumn('items_preview', function($query, $keyword) {
                    $query->whereHas('items', function($q) use ($keyword) {
                        $q->where('item_name', 'like', "%{$keyword}%")
                          ->orWhere('item_code', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('action', function($row){
                    $btn = '<div class="d-flex flex-row gap-2 flex-wrap">';
                    $btn .= '<button class="btn btn-sm btn-info text-white btn-detail" data-id="'.$row->id.'"><i class="ph-bold ph-eye"></i> Detail</button>';
                    $btn .= '<button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'"><i class="ph-bold ph-pencil"></i> Edit</button>';
                    $btn .= '<button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'"><i class="ph-bold ph-trash"></i> Delete</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['items_preview', 'total_items', 'action'])
                ->make(true);
        }

        $availableCustomers = Customer::whereDoesntHave('items')->orderBy('name', 'asc')->get();
        $allCustomers = Customer::orderBy('name', 'asc')->get();

        return view('page.master.customer-item.index', compact('availableCustomers', 'allCustomers'));
    }

    public function getAvailableCustomers()
    {
        $customers = Customer::whereDoesntHave('items')
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'name', 'sort_name']);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ], [
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists'   => 'Customer tidak valid.',
        ]);

        if (CustomerItem::where('customer_id', $request->customer_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Customer ini sudah memiliki data item di list (tidak bisa duplicate). Silakan gunakan tombol Edit untuk mengelola data item customer ini.'
            ], 422);
        }

        $itemsData = [];
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                if (!empty($item['item_name'])) {
                    $itemsData[] = [
                        'customer_id' => $request->customer_id,
                        'item_code'   => $item['item_code'] ?? null,
                        'item_name'   => $item['item_name'],
                        'quantity'    => isset($item['quantity']) && $item['quantity'] !== '' ? (int) $item['quantity'] : 0,
                        'price'       => isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : null,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
        } elseif ($request->filled('item_name')) {
            $itemsData[] = [
                'customer_id' => $request->customer_id,
                'item_code'   => $request->item_code,
                'item_name'   => $request->item_name,
                'quantity'    => $request->filled('quantity') ? (int) $request->quantity : 0,
                'price'       => $request->filled('price') ? (float) $request->price : null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        if (empty($itemsData)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal 1 item dengan Nama Item wajib diisi.'
            ], 422);
        }

        CustomerItem::insert($itemsData);

        return response()->json(['success' => true, 'message' => 'Data Customer Item berhasil disimpan.']);
    }

    public function show($id)
    {
        $customer = Customer::with('items')->findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $itemsData = [];
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                if (!empty($item['item_name'])) {
                    $itemsData[] = [
                        'customer_id' => $customer->id,
                        'item_code'   => $item['item_code'] ?? null,
                        'item_name'   => $item['item_name'],
                        'quantity'    => isset($item['quantity']) && $item['quantity'] !== '' ? (int) $item['quantity'] : 0,
                        'price'       => isset($item['price']) && $item['price'] !== '' ? (float) $item['price'] : null,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
        } elseif ($request->filled('item_name')) {
            $itemsData[] = [
                'customer_id' => $customer->id,
                'item_code'   => $request->item_code,
                'item_name'   => $request->item_name,
                'quantity'    => $request->filled('quantity') ? (int) $request->quantity : 0,
                'price'       => $request->filled('price') ? (float) $request->price : null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        if (empty($itemsData)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal 1 item dengan Nama Item wajib diisi.'
            ], 422);
        }

        CustomerItem::where('customer_id', $customer->id)->delete();
        CustomerItem::insert($itemsData);

        return response()->json(['success' => true, 'message' => 'Data Customer Item berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        CustomerItem::where('customer_id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Data Customer Item berhasil dihapus.']);
    }
}
