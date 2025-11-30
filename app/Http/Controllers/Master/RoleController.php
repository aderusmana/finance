<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::query();

            return datatables()->of($roles)
                ->addIndexColumn()
                ->addColumn('action', function ($role) {
                    return '
                        <div class="d-flex gap-2">
                            <a href="' . route('roles.give-permissions', $role->id) . '" class="btn btn-info" title="Give Permissions">
                                <i class="fa-solid fa-key text-white"></i>
                            </a>
                            <button type="button" class="btn btn-warning btn-edit-role"
                                data-id="' . $role->id . '"
                                data-name="' . e($role->name) . '"
                            >
                                <i class="fa-solid fa-pencil text-white"></i>
                            </button>
                            <form action="' . route('roles.destroy', $role->id) . '" method="POST" class="delete-form delete-role-btn" style="display:inline;">
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

        return view('page.master.roles.index');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

         activity()
           ->causedBy(Auth::user())
           ->performedOn($role)
           ->event('roles')
           ->log('Created a new role');

        return response()->json(['success' => true, 'message' => 'Role created successfully!']);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $oldData = $role->getOriginal();

        $role->update([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        activity()
           ->causedBy(Auth::user())
           ->performedOn($role)
           ->event('roles')
           ->withProperties(['old' => $oldData, 'new' => $role->getChanges()])
           ->log('Updated a role');

        return response()->json(['success' => true, 'message' => 'Role updated successfully!']);
    }

    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $oldData = $role->toArray();
        $role->delete();

        activity()
           ->causedBy(Auth::user())
           ->event('roles')
           ->withProperties(['deleted_data' => $oldData])
           ->log('Deleted a role');

        return response()->json(['success' => true, 'message' => 'Role deleted successfully!']);
    }

    public function addPermissionToRole($roleId)
    {
        $permissions = Permission::get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $role->id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('page.master.roles.add', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function givePermissionToRole(Request $request, $roleId)
    {
        // \dd($request,$roleId);
        $request->validate([
            'permissions' => 'required|array'
        ]);

        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->permissions);
        $oldPermissions = $role->permissions->pluck('name');

        activity()
           ->causedBy(Auth::user())
           ->performedOn($role)
           ->event('roles')
           ->withProperties(['old_permissions' => $oldPermissions, 'new_permissions' => $request->permissions])
           ->log('Updated permissions for a role');

        return response()->json(['success' => true, 'message' => 'Permissions added to role']);
    }
}
