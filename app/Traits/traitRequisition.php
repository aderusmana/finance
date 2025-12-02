<?php

namespace App\Traits;

use App\Models\Master\ApprovalLog;
use App\Models\Master\ApprovalPath;
use App\Models\User;

trait traitRequisition
{
    /**
     * Generate approval logs dari approval path.
     *
     * @param  \App\Models\User  $requester
    * @param  int|string  $relatedId  // Nilai ini menjadi `related_id` di tabel approval_logs (bisa `customer_id` atau `bg_id`)
     * @param  string  $category
     * @param  string|null  $subCategory
     * @return \Illuminate\Support\Collection
     */
    public function generateApprovalLogs($requester, $relatedId, $category, $subCategory = null)
    {
        $query = ApprovalPath::where('category', $category);

        if (!empty($subCategory)) {
            $query->where('sub_category', $subCategory);
        }

        $approvalPath = $query->firstOrFail();

        $sequence = collect($approvalPath->sequence_approvers);
        $logs = collect();

        // Khusus untuk category complain, cek apakah ada head qa di sequence
        // if (strtolower($category) === 'complain') {
        //     $headQaExists = $sequence->contains(function ($role) {
        //         return strtolower($role) === 'head-QA';
        //     });

        //     // Jika ada head qa, pastikan head qa di urutan pertama
        //     if ($headQaExists) {
        //         // Remove head qa dari sequence original dan buat sequence baru
        //         $otherRoles = $sequence->filter(function ($role) {
        //             return strtolower($role) !== 'head-QA';
        //         });

        //         // Gabungkan dengan head qa di urutan pertama
        //         $sequence = collect(['head-QA'])->merge($otherRoles);
        //     }
        // }

        foreach ($sequence as $index => $role) {
            $level = $index + 1;
            if (strtolower($role) === 'atasan') {
                // Ambil NIK atasan requester
                if ($requester->atasan_nik) {
                    $logs->push([
                        'category'       => $category,
                        'related_id'     => $relatedId,
                        'approver_nik'   => $requester->atasan_nik,
                        'status'         => 'Pending',
                        'level'          => $level,
                        'token'          => bin2hex(random_bytes(16)),
                        'notes'          => null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            } else {
                // Ambil semua user dengan role ini
                $users = User::whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                })->first();

                if ($users) {
                    $logs->push([
                        'category'       => $category,
                        'related_id'     => $relatedId,
                        'approver_nik'   => $users->nik,
                        'status'         => 'Pending',
                        'level'          => $level,
                        'token'          => bin2hex(random_bytes(16)),
                        'notes'          => null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }
        }

        // Bulk insert biar lebih cepat
        if ($logs->isNotEmpty()) {
            ApprovalLog::insert($logs->toArray());
        }

        return $logs;
    }
}
