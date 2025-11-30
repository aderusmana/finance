<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $positions = Position::query();

            return DataTables::of($positions)
                ->addIndexColumn()
                ->addColumn('action', function ($position) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-position"
                                data-id="' . $position->id . '"
                                data-position_name="' . e($position->position_name) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('positions.destroy', $position->id) . '" method="POST" class="delete-form delete-position-btn" style="display:inline;">
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

        return view('page.master.positions.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|string|max:255|unique:positions,position_name',
        ]);

        Position::create([
            'position_name' => $request->position_name
        ]);

        return response()->json(['success' => true, 'message' => 'Position created successfully!']);
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'position_name' => 'required|string|max:255|unique:positions,position_name,' . $position->id,
        ]);

        $position->update([
            'position_name' => $request->position_name
        ]);

        return response()->json(['success' => true, 'message' => 'Position updated successfully!']);
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return response()->json(['success' => true, 'message' => 'Position deleted successfully!']);
    }
}
