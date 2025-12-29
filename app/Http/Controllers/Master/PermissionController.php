<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $permissions = Permission::query();

            return DataTables::of($permissions)
                ->addIndexColumn()
                ->addColumn('action', function ($permission) {
                    return '
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-edit-permission"
                                data-id="' . $permission->id . '"
                                data-name="' . e($permission->name) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('permissions.destroy', $permission->id) . '" method="POST" class="delete-form delete-permission-btn" style="display:inline;">
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

        return view('page.master.permissions.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        // Activity Log
        activity()
            ->causedBy(Auth::user())
            ->performedOn($permission)
            ->useLog('master_permission')
            ->event('create')
            ->withProperties(['name' => $permission->name])
            ->log('Created permission: ' . $permission->name);

        return response()->json(['success' => true, 'message' => 'Permission created successfully!']);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $oldData = $permission->getOriginal();

        $permission->update([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        // Activity Log
        activity()
            ->causedBy(Auth::user())
            ->performedOn($permission)
            ->useLog('master_permission')
            ->event('update')
            ->withProperties([
                'old' => $oldData,
                'attributes' => $permission->getChanges()
            ])
            ->log('Updated permission: ' . $permission->name);

        return response()->json(['success' => true, 'message' => 'Permission updated successfully!']);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $oldData = $permission->toArray();
        $permission->delete();

        // Activity Log
        activity()
            ->causedBy(Auth::user())
            ->useLog('master_permission')
            ->event('delete')
            ->withProperties(['deleted_data' => $oldData])
            ->log('Deleted permission: ' . $oldData['name']);

        return response()->json(['success' => true, 'message' => 'Permission deleted successfully!']);
    }
}
