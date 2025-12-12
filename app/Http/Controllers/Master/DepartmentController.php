<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $departments = Department::query();

            return DataTables::of($departments)
                ->addIndexColumn()
                ->addColumn('action', function ($department) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-department"
                                data-id="' . $department->id . '"
                                data-name="' . e($department->name) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('departments.destroy', $department->id) . '" method="POST" class="delete-form delete-department-btn" style="display:inline;">
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

        return view('page.master.departments.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        $department = Department::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        // Activity Log
        activity()
            ->causedBy(Auth::user())
            ->performedOn($department)
            ->useLog('master_department')
            ->event('create')
            ->withProperties(['name' => $department->name])
            ->log('Created new department: ' . $department->name);

        return response()->json(['success' => true, 'message' => 'Department created successfully!']);
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        $oldData = $department->getOriginal(); // Simpan data lama

        $department->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        // Activity Log
        activity()
            ->causedBy(Auth::user())
            ->performedOn($department)
            ->useLog('master_department')
            ->event('update')
            ->withProperties([
                'old' => $oldData,
                'attributes' => $department->getChanges()
            ])
            ->log('Updated department: ' . $department->name);

        return response()->json(['success' => true, 'message' => 'Department updated successfully!']);
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $oldData = $department->toArray(); // Simpan data sebelum hapus

        $department->delete();

        // Activity Log
        activity()
            ->causedBy(Auth::user())
            ->useLog('master_department')
            ->event('delete')
            ->withProperties(['deleted_data' => $oldData])
            ->log('Deleted department: ' . $oldData['name']);

        return response()->json(['success' => true, 'message' => 'Department deleted successfully!']);
    }
}
