<?php

namespace App\Http\Controllers;

use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // [FIX] TAMBAHKAN KEMBALI METHOD INI
    public function fetch()
    {
        $user = Auth::user();

        // 1. Ambil data dasar yang dibutuhkan untuk filter privasi
        $userRequisitionIds = Requisition::where('requester_nik', $user->nik)->pluck('id');
        $pendingApprovalIds = ApprovalLog::where('approver_nik', $user->nik)
                                         ->where('status', 'Pending')
                                         ->pluck('requisition_id');

        // 2. Query utama untuk mengambil notifikasi (bukan activity log)
        $notifications = $user->unreadNotifications()
            ->where(function ($query) use ($user, $userRequisitionIds, $pendingApprovalIds) {
                // Terapkan filter privasi HANYA untuk user biasa
                if (!$user->hasRole('super-admin')) {
                    $query->where(function ($q) use ($userRequisitionIds, $pendingApprovalIds) {
                        // Tampilkan notifikasi jika requisition_id-nya ada di daftar requisition miliknya
                        $q->whereIn('data->requisition_id', $userRequisitionIds);
                        // ATAU tampilkan jika requisition_id-nya ada di daftar tugas approval-nya
                        $q->orWhereIn('data->requisition_id', $pendingApprovalIds);
                    });
                }
                // Super admin tidak perlu filter, jadi dia akan melihat semua unreadNotifications
            })
            ->latest()
            ->limit(20)
            ->get();


        // 3. Ubah (transform) data notifikasi menjadi format yang siap ditampilkan oleh JavaScript
        $formattedNotifications = $notifications->map(function ($notification) {
            $data = $notification->data;
            $message = $data['message'] ?? 'Notification';

            // Tentukan ikon dan warna berdasarkan isi pesan
            $icon = 'iconoir-bell';
            $color = 'bg-secondary';
            if (str_contains(strtolower($message), 'menunggu approval')) {
                $icon = 'iconoir-clock';
                $color = 'bg-warning';
            } elseif (str_contains(strtolower($message), 'telah di-approve')) {
                $icon = 'iconoir-check';
                $color = 'bg-success';
            } elseif (str_contains(strtolower($message), 'telah di-reject') || str_contains(strtolower($message), 'telah di-cancel')) {
                $icon = 'iconoir-cancel';
                $color = 'bg-danger';
            } elseif (str_contains(strtolower($message), 'telah membuat')) {
                $icon = 'iconoir-plus';
                $color = 'bg-primary';
            }

            return [
                'id'    => $notification->id,
                'icon'  => $icon,
                'color' => $color,
                'text'  => $message,
                'time'  => $notification->created_at->diffForHumans(),
                'url'   => $data['url'] ?? '#',
            ];
        });

        return response()->json([
            'notifications' => $formattedNotifications,
        ]);
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Request $request)
    {
        $notification = Auth::user()
                            ->unreadNotifications()
                            ->where('id', $request->id)
                            ->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Mengambil jumlah notifikasi yang belum dibaca.
     */
    public function count()
    {
        $user = Auth::user();

        // Jika super-admin, cukup hitung semua yang belum dibaca karena mereka melihat semuanya.
        if ($user->hasRole('super-admin')) {
            return response()->json([
                'count' => $user->unreadNotifications->count()
            ]);
        }

        // [FIX] Terapkan logika filter yang sama seperti di fetch() untuk user biasa
        $userRequisitionIds = Requisition::where('requester_nik', $user->nik)->pluck('id');
        $pendingApprovalIds = ApprovalLog::where('approver_nik', $user->nik)
                                        ->where('status', 'Pending')
                                        ->pluck('requisition_id');

        $count = $user->unreadNotifications()
            ->where(function ($query) use ($userRequisitionIds, $pendingApprovalIds) {
                $query->where(function ($q) use ($userRequisitionIds, $pendingApprovalIds) {
                    // Notifikasi terkait request yang dia buat
                    $q->whereIn('data->requisition_id', $userRequisitionIds);
                    // ATAU notifikasi terkait tugas approval untuknya
                    $q->orWhereIn('data->requisition_id', $pendingApprovalIds);
                });
            })
            ->count(); // Gunakan ->count() untuk efisiensi

        return response()->json([
            'count' => $count
        ]);
    }
}