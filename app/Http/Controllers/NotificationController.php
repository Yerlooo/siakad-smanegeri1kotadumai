<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all notifications for the authenticated user
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            });

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark specific notification as read.
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // Return JSON response untuk AJAX request (bukan Inertia)
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sebagai dibaca.']);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        $user->unreadNotifications->markAsRead();

        // Return JSON response untuk AJAX request (bukan Inertia)
        if (($request->wantsJson() || $request->ajax()) && !$request->header('X-Inertia')) {
            return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sebagai dibaca.']);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai dibaca.');
    }

    /**
     * Delete specific notification.
     */
    public function destroy(Request $request, $notificationId)
    {
        $user = Auth::user();
        
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->delete();
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Delete all read notifications.
     */
    public function deleteRead(Request $request)
    {
        $user = Auth::user();
        
        $user->notifications()
            ->whereNotNull('read_at')
            ->delete();

        return redirect()->back()->with('success', 'Semua notifikasi yang sudah dibaca berhasil dihapus.');
    }

    /**
     * Get unread notifications count (for header/badge).
     */
    public function getUnreadCount(Request $request)
    {
        // Pastikan ini adalah request AJAX/API bukan Inertia request
        if (!$request->wantsJson() && !$request->ajax()) {
            abort(404);
        }
        
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        
        $count = $user->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for dropdown/bell icon).
     */
    public function getRecent(Request $request)
    {
        // Pastikan ini adalah request AJAX/API bukan Inertia request
        if (!$request->wantsJson() && !$request->ajax()) {
            abort(404);
        }
        
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'notifications' => [],
                'unread_count' => 0
            ]);
        }
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                    'time_ago' => $this->getTimeAgo($notification->created_at),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * Helper method to get time ago format.
     */
    private function getTimeAgo($datetime)
    {
        $now = now();
        $diff = $datetime->diffInMinutes($now);

        if ($diff < 60) {
            return $diff . ' menit yang lalu';
        } elseif ($diff < 1440) { // 24 hours
            $hours = floor($diff / 60);
            return $hours . ' jam yang lalu';
        } elseif ($diff < 10080) { // 7 days
            $days = floor($diff / 1440);
            return $days . ' hari yang lalu';
        } else {
            return $datetime->format('d M Y');
        }
    }
}
