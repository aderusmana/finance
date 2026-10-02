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

            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = $request->nik . '.' . $extension;
            $directory = storage_path('app/public/avatar');
            $path = $directory . '/' . $filename;

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Gunakan PHP GD untuk resize ke 150x150
            $sourceImage = null;
            if (in_array($extension, ['jpg', 'jpeg'])) {
                $sourceImage = @imagecreatefromjpeg($file->getRealPath());
            } elseif ($extension === 'png') {
                $sourceImage = @imagecreatefrompng($file->getRealPath());
            } elseif ($extension === 'webp') {
                $sourceImage = @imagecreatefromwebp($file->getRealPath());
            }

            if ($sourceImage) {
                $width = imagesx($sourceImage);
                $height = imagesy($sourceImage);
                $size = min($width, $height); // Crop jadi kotak sempurna
                $newSize = 150; // Max size 150x150

                $croppedImage = imagecrop($sourceImage, [
                    'x' => ($width - $size) / 2,
                    'y' => ($height - $size) / 2,
                    'width' => $size,
                    'height' => $size
                ]);

                $finalImage = imagecreatetruecolor($newSize, $newSize);

                // Pertahankan transparansi untuk PNG & WEBP
                if (in_array($extension, ['png', 'webp'])) {
                    imagealphablending($finalImage, false);
                    imagesavealpha($finalImage, true);
                    $transparent = imagecolorallocatealpha($finalImage, 255, 255, 255, 127);
                    imagefilledrectangle($finalImage, 0, 0, $newSize, $newSize, $transparent);
                }

                imagecopyresampled($finalImage, $croppedImage, 0, 0, 0, 0, $newSize, $newSize, $size, $size);

                if (in_array($extension, ['jpg', 'jpeg'])) {
                    imagejpeg($finalImage, $path, 90);
                } elseif ($extension === 'png') {
                    imagepng($finalImage, $path);
                } elseif ($extension === 'webp') {
                    imagewebp($finalImage, $path, 90);
                }

                imagedestroy($sourceImage);
                imagedestroy($croppedImage);
                imagedestroy($finalImage);

                $avatarPath = 'avatar/' . $filename;
            } else {
                // Fallback jika ekstensi tidak didukung GD
                $avatarPath = $file->storeAs('avatar', $filename, 'public');
            }

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

