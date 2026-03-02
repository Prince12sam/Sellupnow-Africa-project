<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return paginated notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $page    = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 20), 1);
        $skip    = ($page - 1) * $perPage;

        $userId = auth('api')->id();

        $query = Notification::query()
            ->where('user_id', $userId)
            ->latest('id');

        $total         = $query->count();
        $unreadCount   = (clone $query)->where('is_read', 0)->count();
        $notifications = $query->skip($skip)->take($perPage)->get();

        // Mark all fetched notifications as read
        Notification::query()
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return $this->json('notifications', [
            'total'         => $total,
            'unread_count'  => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function clear(Request $request)
    {
        $userId = auth('api')->id();

        Notification::query()->where('user_id', $userId)->delete();

        return $this->json('All notifications cleared');
    }
}
