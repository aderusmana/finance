<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\TOP;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TOPController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tops = TOP::query();

            return DataTables::of($tops)
                ->addIndexColumn()
                ->addColumn('action', function ($top) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-top"
                                data-id="' . $top->id . '"
                                data-name_top="' . e($top->name_top) . '"
                                data-desc_top="' . e($top->desc_top) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('tops.destroy', $top->id) . '" method="POST" class="delete-form delete-top-btn" style="display:inline;">
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

        return view('page.master.top.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_top' => 'required|string|max:255|unique:tops,name_top',
            'desc_top' => 'nullable|string',
        ]);

        TOP::create([
            'name_top' => $request->name_top,
            'desc_top' => $request->desc_top,
        ]);

        return response()->json(['success' => true, 'message' => 'TOP created successfully!']);
    }

    public function update(Request $request, TOP $top)
    {
        $request->validate([
            'name_top' => 'required|string|max:255|unique:tops,name_top,' . $top->id,
            'desc_top' => 'nullable|string',
        ]);

        $top->update([
            'name_top' => $request->name_top,
            'desc_top' => $request->desc_top,
        ]);

        return response()->json(['success' => true, 'message' => 'TOP updated successfully!']);
    }

    public function destroy($id)
    {
        $top = TOP::findOrFail($id);
        $top->delete();

        return response()->json(['success' => true, 'message' => 'TOP deleted successfully!']);
    }
}
