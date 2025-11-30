<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\ItemMaster;
use App\Models\Master\ItemDetail;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    //
    public function indexMaster(Request $request)
    {
        // If the request is an AJAX call (DataTables), return JSON using Yajra
        if ($request->ajax()) {
            $query = ItemMaster::with('ItemDetails');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = '<button type="button" class="btn btn-sm btn-primary edit-item" data-id="' . $row->id . '">Edit</button>';
                    $delete = '<button type="button" class="btn btn-sm btn-danger delete-item" data-id="' . $row->id . '">Delete</button>';
                    return $edit . ' ' . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }


        // Otherwise return the normal view
        return view('page.master.items.item-master');
    }

    /**
     * Store a newly created ItemMaster.
     * Returns JSON response.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_master_code' => 'required|string|max:100|unique:item_masters,item_master_code',
            'item_master_name' => 'required|string|max:191',
            'unit' => 'required|string|max:50',
        ]);

        try {
            $item = ItemMaster::create($validated);
            return response()->json([ 'message' => 'Item created', 'data' => $item ], 201);
        } catch (\Exception $e) {
            return response()->json([ 'message' => 'Unable to create item', 'error' => $e->getMessage() ], 500);
        }
    }

    /**
     * Return a single item as JSON for editing.
     */
    public function edit($id)
    {
        $item = ItemMaster::with('ItemDetails')->find($id);
        if (! $item) {
            return response()->json([ 'message' => 'Item not found' ], 404);
        }
        return response()->json([ 'data' => $item ], 200);
    }

    /**
     * Update an existing ItemMaster.
     */
    public function update(Request $request, $id)
    {
        $item = ItemMaster::find($id);
        if (! $item) {
            return response()->json([ 'message' => 'Item not found' ], 404);
        }

        $validated = $request->validate([
            'item_master_code' => 'required|string|max:100|unique:item_masters,item_master_code,' . $id,
            'item_master_name' => 'required|string|max:191',
            'unit' => 'required|string|max:50',
        ]);

        try {
            $item->update($validated);
            return response()->json([ 'message' => 'Item updated', 'data' => $item ], 200);
        } catch (\Exception $e) {
            return response()->json([ 'message' => 'Unable to update item', 'error' => $e->getMessage() ], 500);
        }
    }

    /**
     * Delete an ItemMaster.
     */
    public function destroy($id)
    {
        $item = ItemMaster::find($id);
        if (! $item) {
            return response()->json([ 'message' => 'Item not found' ], 404);
        }

        try {
            $item->delete();
            return response()->json([ 'message' => 'Item deleted' ], 200);
        } catch (\Exception $e) {
            return response()->json([ 'message' => 'Unable to delete item', 'error' => $e->getMessage() ], 500);
        }
    }

    /**
     * DataTables JSON for all item details or return page for all-details
     */
    public function detailsAll(Request $request)
    {
        if ($request->ajax()) {
            $query = ItemDetail::with('itemMaster');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('item_master_name', function ($row) {
                    return $row->itemMaster->item_master_name ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $edit = '<button type="button" class="btn btn-sm btn-primary edit-item-detail" data-id="' . $row->id . '">Edit</button>';
                    $delete = '<button type="button" class="btn btn-sm btn-danger delete-item-detail" data-id="' . $row->id . '">Delete</button>';
                    return $edit . ' ' . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $itemMasters = ItemMaster::all();

        return view('page.master.items.item-detail', compact('itemMasters'));
    }

    public function storeDetailAll(Request $request)
    {
        $validated = $request->validate([
            'item_master_id' => 'required|integer|exists:item_masters,id',
            'component_item_master_id' => 'nullable|integer|exists:item_masters,id',
            'material_type' => 'nullable|string|max:100',
            'item_detail_code' => 'required|string|max:100|unique:item_details,item_detail_code',
            'item_detail_name' => 'required|string|max:191',
            'unit' => 'nullable|string|max:50',
            'net_weight' => 'nullable|numeric',
        ]);

        try {
            $detail = ItemDetail::create($validated);
            return response()->json(['message' => 'Item detail created', 'data' => $detail], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to create item detail', 'error' => $e->getMessage()], 500);
        }
    }

    public function editDetailAll($id)
    {
        $detail = ItemDetail::with('itemMaster')->find($id);
        if (! $detail) {
            return response()->json(['message' => 'Item detail not found'], 404);
        }
        return response()->json(['data' => $detail], 200);
    }

    /**
     * Per-item details (list page or DataTables JSON)
     */
    public function detailsIndex(Request $request, $item_id)
    {
        if ($request->ajax()) {
            $query = ItemDetail::where('item_master_id', $item_id);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = '<button type="button" class="btn btn-sm btn-primary edit-item-detail" data-id="' . $row->id . '">Edit</button>';
                    $delete = '<button type="button" class="btn btn-sm btn-danger delete-item-detail" data-id="' . $row->id . '">Delete</button>';
                    return $edit . ' ' . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $itemMaster = ItemMaster::find($item_id);
        $itemMasters = ItemMaster::all();

        return view('page.master.items.item-detail', compact('itemMaster','itemMasters'));
    }

    public function storeDetail(Request $request, $item_id)
    {
        $validated = $request->validate([
            'component_item_master_id' => 'nullable|integer|exists:item_masters,id',
            'material_type' => 'nullable|string|max:100',
            'item_detail_code' => 'required|string|max:100|unique:item_details,item_detail_code',
            'item_detail_name' => 'required|string|max:191',
            'unit' => 'nullable|string|max:50',
            'net_weight' => 'nullable|numeric',
        ]);

        $validated['item_master_id'] = $item_id;

        try {
            $detail = ItemDetail::create($validated);
            return response()->json(['message' => 'Item detail created', 'data' => $detail], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to create item detail', 'error' => $e->getMessage()], 500);
        }
    }

    public function editDetail($item_id, $id)
    {
        $detail = ItemDetail::find($id);
        if (! $detail) {
            return response()->json(['message' => 'Item detail not found'], 404);
        }
        return response()->json(['data' => $detail], 200);
    }

    public function updateDetail(Request $request, $item_id, $id)
    {
        $detail = ItemDetail::find($id);
        if (! $detail) {
            return response()->json(['message' => 'Item detail not found'], 404);
        }

        $validated = $request->validate([
            'component_item_master_id' => 'nullable|integer|exists:item_masters,id',
            'material_type' => 'nullable|string|max:100',
            'item_detail_code' => 'required|string|max:100|unique:item_details,item_detail_code,' . $id,
            'item_detail_name' => 'required|string|max:191',
            'unit' => 'nullable|string|max:50',
            'net_weight' => 'nullable|numeric',
        ]);

        $validated['item_master_id'] = $item_id;

        try {
            $detail->update($validated);
            return response()->json(['message' => 'Item detail updated', 'data' => $detail], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to update item detail', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyDetail($item_id, $id)
    {
        $detail = ItemDetail::find($id);
        if (! $detail) {
            return response()->json(['message' => 'Item detail not found'], 404);
        }

        try {
            $detail->delete();
            return response()->json(['message' => 'Item detail deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to delete item detail', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Return ItemMaster options for Select2 AJAX.
     */
    public function select2(Request $request)
    {
        $q = $request->get('q', '');
        $query = ItemMaster::select('id', 'item_master_name');
        if ($q) {
            $query->where('item_master_name', 'like', "%{$q}%");
        }
        $items = $query->limit(50)->get();

        $results = $items->map(function ($item) {
            return ['id' => $item->id, 'text' => $item->item_master_name];
        });

        return response()->json(['results' => $results]);
    }

    public function updateDetailAll(Request $request, $id)
    {
        $detail = ItemDetail::find($id);
        if (! $detail) {
            return response()->json(['message' => 'Item detail not found'], 404);
        }

        $validated = $request->validate([
            'item_master_id' => 'required|integer|exists:item_masters,id',
            'component_item_master_id' => 'nullable|integer|exists:item_masters,id',
            'material_type' => 'nullable|string|max:100',
            'item_detail_code' => 'required|string|max:100|unique:item_details,item_detail_code,' . $id,
            'item_detail_name' => 'required|string|max:191',
            'unit' => 'nullable|string|max:50',
            'net_weight' => 'nullable|numeric',
        ]);

        try {
            $detail->update($validated);
            return response()->json(['message' => 'Item detail updated', 'data' => $detail], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to update item detail', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyDetailAll($id)
    {
        $detail = ItemDetail::find($id);
        if (! $detail) {
            return response()->json(['message' => 'Item detail not found'], 404);
        }

        try {
            $detail->delete();
            return response()->json(['message' => 'Item detail deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable to delete item detail', 'error' => $e->getMessage()], 500);
        }
    }


}
