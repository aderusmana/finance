<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Mengisi data user dari request yang sudah divalidasi
        $user = $request->user();
        $user->fill($request->validated());

        // Jika user mengubah email, reset verifikasi email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Menangani unggahan avatar dengan nama file NIK
        if ($request->hasFile('avatar')) {
            // PERBAIKAN: Cari dan hapus semua file avatar lama yang cocok dengan NIK.
            // Ambil semua file dari direktori 'avatar'
            $allAvatarFiles = Storage::disk('public')->files('avatar');

            // Filter untuk menemukan file yang namanya diawali dengan NIK pengguna.
            $filesToDelete = array_filter($allAvatarFiles, function ($file) use ($user) {
                // $file akan berisi path seperti 'avatar/AG1315.jpeg'
                // basename($file) akan mengambil 'AG1315.jpeg'
                return str_starts_with(basename($file), $user->nik . '.');
            });

            if (!empty($filesToDelete)) {
                Storage::disk('public')->delete($filesToDelete);
            }

            // ambil ekstensi file (jpg/png/dll)
            $extension = $request->file('avatar')->getClientOriginalExtension();

            // simpan ke disk "public" (storage/app/public/avatar)
            // Menggunakan $request->nik karena $user->nik mungkin belum tersimpan jika NIK juga diubah
            $avatarPath = $request->file('avatar')->storeAs(
                'avatar',                               // folder di dalam storage/app/public
                $request->nik . '.' . $extension,       // nama file = NIK.ext
                'public'                                // pakai disk public, bukan local
            );

            // simpan path relatif untuk dipanggil dengan asset()
            $user->avatar = 'storage/' . $avatarPath;
        }

        // Simpan semua perubahan ke database
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Hapus file avatar jika ada sebelum menghapus user
        if ($user->avatar) {
            // Perbaikan: Hapus 'storage/' dari path sebelum menghapus file
            $avatarPathToDelete = str_replace('storage/', '', $user->avatar);
            Storage::disk('public')->delete($avatarPathToDelete);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

