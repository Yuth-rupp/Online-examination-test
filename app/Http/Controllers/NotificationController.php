<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Shared notification endpoint used by Dashboard, History, Exams,
 * and Settings (and polled as a fallback by Support). The real-time
 * push happens via App\Events\UserNotificationCreated over the
 * `notifications.{userId}` private channel; these HTTP endpoints
 * handle the initial load, read/unread state, and clearing.
 */
class NotificationController extends Controller
{
    /**
     * Latest notifications for the authenticated user (any role).
     */
    public function index(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $notifications = Notification::where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->map(fn ($n) => [
                'id'    => $n->id,
                'title' => $n->title,
                'body'  => $n->body,
                'type'  => $n->type,
                'data'  => $n->data,
                'read'  => (bool) $n->read_at,
                'time'  => $n->created_at?->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $notifications->where('read', false)->count(),
        ]);
    }

    /**
     * Lightweight endpoint just for the bell badge count.
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $count = Notification::where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, $id)
    {
        $user = $request->user() ?? Auth::user();

        $notification = Notification::where('user_id', $user->user_id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found.'], 404);
        }

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark every notification for this user as read.
     */
    public function markAllRead(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        Notification::where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Permanently delete all notifications for this user.
     */
    public function clearAll(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        Notification::where('user_id', $user->user_id)->delete();

        return response()->json(['success' => true]);
    }
}
