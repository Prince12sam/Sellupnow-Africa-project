<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth('web')->user()
            ->notifications()
            ->latest()
            ->paginate(20);
        return view('frontend.user.notifications.index', compact('notifications'));
    }

    public function read_notification()
    {
        auth('web')->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
