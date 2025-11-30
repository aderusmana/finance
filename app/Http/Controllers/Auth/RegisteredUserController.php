<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = \App\Models\Master\Department::all();
        return view('auth.register', compact('departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nik' => ['required', 'string', 'unique:users,nik'],
            'username' => ['required', 'string', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'atasan_nik' => ['nullable', 'string', 'exists:users,nik'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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
            'email' => $request->email,
            'name' => $request->name,
            'avatar' => $avatarPath,
            'department_id' => $request->department_id,
            'atasan_nik' => $request->atasan_nik,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
