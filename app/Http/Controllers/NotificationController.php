<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mengambil list notifikasi untuk dropdown header
     */
    public function fetch()
    {
        $user = Auth::user();

        // Ambil 10 notifikasi terbaru (Read & Unread)
        $notifications = $user->notifications()->limit(10)->get()->map(function($n) {
            return [
                'id' => $n->id,
                'data' => $n->data,
                'read_at' => $n->read_at,
                'created_at' => $n->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Menghitung jumlah badge (Hanya Unread)
     */
    public function count()
    {
        $count = Auth::user()->unreadNotifications->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca
     */
    public function markAsRead(Request $request)
    {
        $notification = Auth::user()
                            ->unreadNotifications()
                            ->where('id', $request->id)
                            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        // Return JSON saja, jangan redirect dari sini
        return response()->json(['success' => true]);
    }

    /**
     * DELETE: Hapus notifikasi
     */
    public function destroy($id)
    {
        $notification = Auth::user()
                        ->notifications()
                        ->where('id', $id)
                        ->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan'], 404);
    }

    /**
     * Tandai semua sudah dibaca
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
