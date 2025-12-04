<?php

namespace App\Traits;

use App\Models\Master\ApprovalLog;
use App\Models\Master\ApprovalPath;
use App\Models\User;
use Illuminate\Support\Facades\Log;

trait ApprovalTrait
{
    public function generateApprovalLogs($requester, $relatedId, $category, $pathSubCategory = null)
    {
        Log::info("START Approval Generation: Category: $category, ID: $relatedId, Sub: " . ($pathSubCategory ?? 'NULL'));

        // 1. CARI PATH YANG COCOK DI DATABASE
        $query = ApprovalPath::where('category', $category);

        if (!empty($pathSubCategory)) {
            // Cari sub category spesifik (misal: 'CBD', 'BG')
            $query->where('sub_category', $pathSubCategory);
        } else {
            // Cari sub category umum/null (misal: Net 30 Days)
            $query->where(function($q) {
                $q->whereNull('sub_category')
                  ->orWhere('sub_category', '');
            });
        }

        $approvalPath = $query->first();

        if (!$approvalPath) {
            Log::warning("Path Approval TIDAK DITEMUKAN. Cek tabel approval_paths untuk: $category - " . ($pathSubCategory ?? 'NULL'));
            return collect(); // Balikkan kosong agar tidak error
        }

        // 2. PARSING URUTAN DARI DATABASE
        // Logic ini menerima format simple array string: ["atasan", "head-SNM"]
        // URUTAN dalam array ini yang menentukan Level 1, Level 2, dst.
        $targetSequence = $approvalPath->sequence_approvers;

        if (is_string($targetSequence)) {
            $targetSequence = json_decode($targetSequence, true);
        }

        if (!is_array($targetSequence) || empty($targetSequence)) {
            Log::error("Format sequence_approvers di Database SALAH atau KOSONG. ID Path: " . $approvalPath->id);
            return collect();
        }

        $logs = collect();

        // 3. LOOPING UNTUK MENENTUKAN LEVEL DAN USER
        foreach ($targetSequence as $index => $approverString) {

            // Level ditentukan otomatis berdasarkan urutan array (0 jadi Level 1, 1 jadi Level 2)
            $level = $index + 1;

            $approverNik = null;

            // Bersihkan spasi kiri kanan (trim) dan ubah ke huruf kecil untuk pengecekan logic
            $approverClean = trim($approverString);
            $approverLower = strtolower($approverClean);

            // --- LOGIC PENCARIAN USER ---

            if ($approverLower === 'atasan') {
                // A. TIPE ATASAN (Dinamis: Mengambil atasan si Requester)
                $approverNik = $requester->atasan_nik;

                if (!$approverNik) {
                    Log::error("Gagal generate step $level: User {$requester->name} tidak punya Data Atasan (atasan_nik null).");
                }

            } else {
                // B. TIPE ROLE / JABATAN (Static: Mencari user pemegang role tersebut)
                // Contoh: "Finance Manager", "head-SNM"

                // Cek apakah stringnya adalah NIK spesifik (angka semua)?
                if (is_numeric($approverClean) && strlen($approverClean) >= 3) {
                     // B1. Spesifik User by NIK
                     $approverNik = $approverClean;
                } else {
                     // B2. Cari berdasarkan Nama Role di tabel User/Roles
                     // Menggunakan whereHas (Asumsi pakai Spatie/Relasi roles)
                     $userWithRole = User::whereHas('roles', function ($q) use ($approverClean) {
                        $q->where('name', $approverClean);
                     })->first();

                     if ($userWithRole) {
                        $approverNik = $userWithRole->nik;
                     } else {
                        Log::error("Gagal generate step $level: Tidak ada user dengan Role '$approverClean'.");
                     }
                }
            }

            // 4. INSERT KE LOGS (Hanya jika usernya ketemu)
            if ($approverNik) {
                $logs->push([
                    'category'       => $category,
                    'related_id'     => $relatedId,
                    'approver_nik'   => $approverNik,
                    'status'         => 'Pending',
                    'level'          => $level,
                    'token'          => bin2hex(random_bytes(16)), // Token unik untuk email approval link
                    'notes'          => null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        // Simpan Batch ke Database
        if ($logs->isNotEmpty()) {
            ApprovalLog::insert($logs->toArray());
            Log::info("Berhasil membuat " . $logs->count() . " approval logs.");
        } else {
            Log::warning("Approval Log kosong. Cek apakah atasan_nik user terisi atau Role user tersedia.");
        }

        return $logs;
    }
}
