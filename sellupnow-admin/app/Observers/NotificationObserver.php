<?php

namespace App\Observers;

use App\Models\Notification;
use App\Services\PushNotificationService;

class NotificationObserver
{
    /**
     * Fires a push notification whenever a Notification row is inserted.
     */
    public function created(Notification $notification): void
    {
        if (! $notification->user_id) {
            return;
        }

        PushNotificationService::sendToUsers(
            $notification->user_id,
            $notification->title ?? 'New notification',
            $notification->content ?? '',
            [
                'type' => $notification->type ?? 'general',
                'url'  => $notification->url  ?? '',
                'icon' => $notification->icon ?? '',
            ]
        );
    }
}
