<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\AccountGroup;
use App\Models\Customer\Branch;
use App\Models\Customer\Regions;
use App\Models\Customer\Sales;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Use an Eloquent query and select the sales table columns explicitly to allow relation filtering
            $query = Sales::with(['user', 'accountGroup', 'branch', 'region'])->select('sales.*');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                // add columns from relations for client-side display
                ->addColumn('user_name', function ($sale) {
                    return $sale->user?->name ?? '-';
                })
                ->addColumn('position_name', function ($sale) {
                    return $sale->user->position?->position_name ?? '-';
                })
                ->filterColumn('position_name', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->whereHas('position', function ($qq) use ($keyword) {
                            $qq->where('position_name', 'like', "%{$keyword}%");
                        });
                    });
                })
                ->addColumn('account_group_name', function ($sale) {
                    return $sale->accountGroup?->name_account_group ?? '-';
                })
                ->addColumn('branch_name', function ($sale) {
                    return $sale->branch?->branch_name ?? '-';
                })
                ->addColumn('region_name', function ($sale) {
                    return $sale->region?->region_name ?? '-';
                })
                ->addColumn('action', function ($sale) {
                    $dataAttrs = '';
                    $dataAttrs .= ' data-id="' . $sale->id . '"';
                    $dataAttrs .= ' data-user_id="' . e($sale->user_id) . '"';
                    $dataAttrs .= ' data-user_position="' . e($sale->user?->position?->position_name) . '"';
                    $dataAttrs .= ' data-account_group_id="' . e($sale->account_group_id) . '"';
                    $dataAttrs .= ' data-branch_id="' . e($sale->branch_id) . '"';
                    $dataAttrs .= ' data-region_id="' . e($sale->region_id) . '"';

                    return '<div class="d-flex gap-2">'
                        . '<button type="button" class="btn btn-warning btn-edit-sale"' . $dataAttrs . '>'
                        . '<i class="fa-solid fa-pencil text-white"></i></button>'
                        . '<form action="' . route('sales.destroy', $sale->id) . '" method="POST" class="delete-form delete-sale-btn" style="display:inline;">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt text-white"></i></button></form>'
                        . '</div>';
                })
                // allow searching by related columns
                ->filterColumn('user_name', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('account_group_name', function ($query, $keyword) {
                    $query->whereHas('accountGroup', function ($q) use ($keyword) {
                        $q->where('name_account_group', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('branch_name', function ($query, $keyword) {
                    $query->whereHas('branch', function ($q) use ($keyword) {
                        $q->where('branch_name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('region_name', function ($query, $keyword) {
                    $query->whereHas('region', function ($q) use ($keyword) {
                        $q->where('region_name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        // eager-load position so the view can show user position without extra queries
        $users = User::role('sales')->with('position')->get();
        $accountGroups = AccountGroup::all();
        $branches = Branch::all();
        $regions = Regions::all();

        return view('page.master.sales.index', compact('users', 'accountGroups', 'branches', 'regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'account_group_id' => 'required|exists:account_groups,id',
            'branch_id' => 'required|exists:branches,id',
            'region_id' => 'required|exists:regions,id',
        ]);

        Sales::create($request->only([
            'user_id', 'account_group_id', 'branch_id', 'region_id'
        ]));

        return response()->json(['success' => true, 'message' => 'Sales created successfully!']);
    }

    public function update(Request $request, Sales $sale)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'account_group_id' => 'required|exists:account_groups,id',
            'branch_id' => 'required|exists:branches,id',
            'region_id' => 'required|exists:regions,id',
        ]);

        $sale->update($request->only([
            'user_id', 'account_group_id', 'branch_id', 'region_id'
        ]));

        return response()->json(['success' => true, 'message' => 'Sales updated successfully!']);
    }

    public function destroy($id)
    {
        $sale = Sales::findOrFail($id);
        $sale->delete();

        return response()->json(['success' => true, 'message' => 'Sales deleted successfully!']);
    }
}
