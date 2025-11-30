<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\AccountGroup;
use Illuminate\Http\Request;

class AccountGroupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $accountGroups = AccountGroup::query();

            return \Yajra\DataTables\Facades\DataTables::of($accountGroups)
                ->addIndexColumn()
                ->addColumn('action', function ($ag) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-account-group"
                                data-id="' . $ag->id . '"
                                data-name_account_group="' . e($ag->name_account_group) . '"
                                data-bank_garansi="' . e($ag->bank_garansi) . '"
                                data-ccar="' . e($ag->ccar) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('account-groups.destroy', $ag->id) . '" method="POST" class="delete-form delete-account-group-btn" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt text-white"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('page.master.account-group.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_account_group' => 'required|string|max:255|unique:account_groups,name_account_group',
            'bank_garansi' => 'nullable|string|max:255',
            'ccar' => 'nullable|string|max:255',
        ]);

        AccountGroup::create([
            'name_account_group' => $request->name_account_group,
            'bank_garansi' => $request->bank_garansi,
            'ccar' => $request->ccar,
        ]);

        return response()->json(['success' => true, 'message' => 'Account group created successfully!']);
    }

    public function update(Request $request, AccountGroup $accountGroup)
    {
        $request->validate([
            'name_account_group' => 'required|string|max:255|unique:account_groups,name_account_group,' . $accountGroup->id,
            'bank_garansi' => 'nullable|string|max:255',
            'ccar' => 'nullable|string|max:255',
        ]);

        $accountGroup->update([
            'name_account_group' => $request->name_account_group,
            'bank_garansi' => $request->bank_garansi,
            'ccar' => $request->ccar,
        ]);

        return response()->json(['success' => true, 'message' => 'Account group updated successfully!']);
    }

    public function destroy($id)
    {
        $accountGroup = AccountGroup::findOrFail($id);
        $accountGroup->delete();

        return response()->json(['success' => true, 'message' => 'Account group deleted successfully!']);
    }
}
