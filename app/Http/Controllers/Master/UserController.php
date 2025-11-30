<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Department;
use App\Models\Master\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    //  public function __construct()
    // {
    //     $this->middleware('permission:view user', ['only' => ['index']]);
    //     $this->middleware('permission:create user', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:update user', ['only' => ['update', 'edit']]);
    //     $this->middleware('permission:updateProfile user', ['only' => ['updateProfile']]);
    //     $this->middleware('permission:delete user', ['only' => ['destroy']]);
    // }

    public function index()
    {
        $departments = Department::all();
        $positions = Position::all();
        $roles = Role::all();
        // list of users for selects (id => name)
        $user = User::pluck('name', 'id');

        // list of possible supervisors by NIK (nik => name). Can be null in the form/view.
        $atasans = User::pluck('name', 'nik');

        return view('page.master.users.index', compact('roles', 'departments', 'positions', 'user', 'atasans'));
    }

    public function getData()
    {
        $users = User::with(['department', 'roles']);

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('name', function ($user) {
                $avatar = $user->avatar
                    ? asset($user->avatar)
                    : asset('assets/images/logo/sinarmeadow.png');

                return '
                    <div class="d-flex align-items-center">
                        <div class="h-30 w-30 d-flex-center b-r-50 overflow-hidden text-bg-dark">
                            <img src="' . $avatar . '" alt="avatar" class="img-fluid">
                        </div>
                        <p class="mb-0 ps-2">' . e($user->name) . '</p>
                    </div>
                ';
            })
            ->addColumn('roles', function ($user) {
                $badges = '';
                foreach ($user->roles as $role) {
                    $badges .= '<span class="badge bg-primary me-1">' . e($role->name) . '</span>';
                }
                return $badges;
            })
            ->addColumn('department', function ($user) {
                return $user->department ? e($user->department->name) : '-';
            })
            ->addColumn('position', function ($user) {
                return $user->position ? e($user->position->position_name) : '-';
            })
            ->addColumn('action', function ($user) {
                $roles = $user->roles->pluck('name')->toArray();

                        return '
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning btn-edit-user"
                            data-id="' . $user->id . '"
                            data-nik="' . $user->nik . '"
                            data-username="' . $user->username . '"
                            data-name="' . e($user->name) . '"
                            data-email="' . e($user->email) . '"
                            data-department_id="' . $user->department_id . '"
                            data-position_id="' . $user->position_id . '"
                            data-atasan_nik="' . e($user->atasan_nik) . '"
                            data-roles=\'' . json_encode($roles) . '\'
                            data-avatar="' . $user->avatar . '"
                            data-status="' . $user->status . '"
                        >
                            <i class="fa-solid fa-pencil text-white"></i>
                        </button>

                        <form action="' . route('users.destroy', $user->id) . '" method="POST" class="delete-form delete-user-btn" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt text-white"></i>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['name', 'roles', 'action'])
            ->make(true);
    }


    public function store(Request $request)
    {


        $request->validate([
            'nik' => 'required|min:4|max:6|unique:users,nik',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'atasan_nik' => 'nullable|exists:users,nik',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name', // pakai id, bukan name
        ]);

         $avatarPath = null;

        if ($request->hasFile('avatar')) {
            // ambil ekstensi file (jpg/png/dll)
            $extension = $request->file('avatar')->getClientOriginalExtension();

            // simpan ke disk "public" (storage/app/public/avatar)
            $avatarPath = $request->file('avatar')->storeAs(
                'avatar',                          // folder di dalam storage/app/public
                $request->nik . '.' . $extension,  // nama file = NIK
                'public'                           // pakai disk public, bukan local
            );

            // simpan path relatif untuk dipanggil dengan asset()
            $avatarPath = 'storage/' . $avatarPath;
        }

        $user = User::create([
            'nik' => $request->nik,
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'atasan_nik' => $request->atasan_nik,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'status' => 'active',
            'avatar' => $avatarPath,
        ]);

            $user->syncRoles($request->roles);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->event('users')
                ->log('Created a new user');

        // Return JSON for AJAX
        return response()->json(['success' => true, 'message' => 'User created successfully!']);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nik' => 'required|min:4|max:6|unique:users,nik,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|max:20',
            'atasan_nik' => 'nullable|exists:users,nik',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
        ]);

        $oldData = $user->getOriginal();
        $oldRoles = $user->getRoleNames();

        $data = $request->only([
            'nik',
            'username',
            'name',
            'atasan_nik',
            'email',
            'position_id',
            'department_id',
            'status',
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

           // Menangani unggahan avatar dengan nama file NIK
        if ($request->hasFile('avatar')) {
            $allAvatarFiles = Storage::disk('public')->files('avatar');

            // Filter untuk menemukan file yang namanya diawali dengan NIK pengguna.
            $filesToDelete = array_filter($allAvatarFiles, function ($file) use ($user) {
                return str_starts_with(basename($file), $user->nik . '.');
            });

            if (!empty($filesToDelete)) {
                Storage::disk('public')->delete($filesToDelete);
            }

            // ambil ekstensi file (jpg/png/dll)
            $extension = $request->file('avatar')->getClientOriginalExtension();

            // simpan ke disk "public" (storage/app/public/avatar)
            $avatarPath = $request->file('avatar')->storeAs(
                'avatar',                               // folder di dalam storage/app/public
                $request->nik . '.' . $extension,       // nama file = NIK.ext
                'public'                                // pakai disk public, bukan local
            );

            // simpan path relatif untuk dipanggil dengan asset()
            $user->avatar = 'storage/' . $avatarPath;
        }

        // Update user
        $user->update($data);

        // Sync roles
        $user->syncRoles($request->roles);

        activity()
           ->causedBy(Auth::user())
           ->performedOn($user)
           ->event('users')
           ->withProperties([
               'old' => array_merge($oldData, ['roles' => $oldRoles]),
               'new' => array_merge($user->getChanges(), ['roles' => $request->roles])
            ])
           ->log('Updated user data');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!'
        ]);
    }


    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $oldData = $user->toArray();

        // Hapus file avatar jika ada sebelum menghapus user
        if ($user->avatar) {
            // Perbaikan: Hapus 'storage/' dari path sebelum menghapus file
            $avatarPathToDelete = str_replace('storage/', '', $user->avatar);
            Storage::disk('public')->delete($avatarPathToDelete);
        }

        $user->delete();

        activity()
           ->causedBy(Auth::user())
           ->event('users')
           ->withProperties(['deleted_data' => $oldData])
           ->log('Deleted a user');

        // Return JSON for AJAX
        return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
    }
}
