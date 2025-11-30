<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Regions;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $regions = Regions::query();

            return \Yajra\DataTables\Facades\DataTables::of($regions)
                ->addIndexColumn()
                ->addColumn('action', function ($region) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-region"
                                data-id="' . $region->id . '"
                                data-region_name="' . e($region->region_name) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('regions.destroy', $region->id) . '" method="POST" class="delete-form delete-region-btn" style="display:inline;">
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

        return view('page.master.regions.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'region_name' => 'required|string|max:255|unique:regions,region_name',
        ]);

        Regions::create([
            'region_name' => $request->region_name,
        ]);

        return response()->json(['success' => true, 'message' => 'Region created successfully!']);
    }

    public function update(Request $request, Regions $region)
    {
        $request->validate([
            'region_name' => 'required|string|max:255|unique:regions,region_name,' . $region->id,
        ]);

        $region->update([
            'region_name' => $request->region_name,
        ]);

        return response()->json(['success' => true, 'message' => 'Region updated successfully!']);
    }

    public function destroy($id)
    {
        $region = Regions::findOrFail($id);
        $region->delete();

        return response()->json(['success' => true, 'message' => 'Region deleted successfully!']);
    }
}
