<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $branches = Branch::query();

            return \Yajra\DataTables\Facades\DataTables::of($branches)
                ->addIndexColumn()
                ->addColumn('action', function ($branch) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-branch"
                                data-id="' . $branch->id . '"
                                data-branch_name="' . e($branch->branch_name) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('branches.destroy', $branch->id) . '" method="POST" class="delete-form delete-branch-btn" style="display:inline;">
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

        return view('page.master.branch.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255|unique:branches,branch_name',
        ]);

        Branch::create([
            'branch_name' => $request->branch_name,
        ]);

        return response()->json(['success' => true, 'message' => 'Branch created successfully!']);
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255|unique:branches,branch_name,' . $branch->id,
        ]);

        $branch->update([
            'branch_name' => $request->branch_name,
        ]);

        return response()->json(['success' => true, 'message' => 'Branch updated successfully!']);
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        return response()->json(['success' => true, 'message' => 'Branch deleted successfully!']);
    }
}
