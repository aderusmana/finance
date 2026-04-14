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
        $query = ApprovalPath::where('category', $category);

        if (!empty($pathSubCategory)) {
            $query->where('sub_category', $pathSubCategory);
        } else {
            $query->where(function($q) {
                $q->whereNull('sub_category')
                  ->orWhere('sub_category', '');
            });
        }

        $approvalPath = $query->first();

        if (!$approvalPath) {
            Log::warning("Path Approval TIDAK DITEMUKAN. Cek tabel approval_paths untuk: $category - " . ($pathSubCategory ?? 'NULL'));
            return collect();
        }

        $targetSequence = $approvalPath->sequence_approvers;

        if (is_string($targetSequence)) {
            $targetSequence = json_decode($targetSequence, true);
        }

        if (!is_array($targetSequence) || empty($targetSequence)) {
            Log::error("Format sequence_approvers di Database SALAH atau KOSONG. ID Path: " . $approvalPath->id);
            return collect();
        }

        $logs = collect();

        foreach ($targetSequence as $index => $approverString) {
            $level = $index + 1;
            $approverNik = null;
            $approverClean = trim($approverString);
            $approverLower = strtolower($approverClean);

            if ($approverLower === 'atasan') {
                $approverNik = $requester->atasan_nik;

                if (!$approverNik) {
                    Log::error("Gagal generate step $level: User {$requester->name} tidak punya Data Atasan (atasan_nik null).");
                }

            } else {
                if (is_numeric($approverClean) && strlen($approverClean) >= 3) {
                     $approverNik = $approverClean;
                } else {
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

            if ($approverNik) {
                $logs->push([
                    'category'       => $category,
                    'sub_category'   => $pathSubCategory,
                    'related_id'     => $relatedId,
                    'approver_nik'   => $approverNik,
                    'status'         => 'Pending',
                    'level'          => $level,
                    'token'          => bin2hex(random_bytes(16)),
                    'notes'          => null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        if ($logs->isNotEmpty()) {
            ApprovalLog::insert($logs->toArray());
            Log::info("Berhasil membuat " . $logs->count() . " approval logs.");
        } else {
            Log::warning("Approval Log kosong. Cek apakah atasan_nik user terisi atau Role user tersedia.");
        }

        return $logs;
    }
}
