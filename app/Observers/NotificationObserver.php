<?php

namespace App\Observers;

use App\Events\UserNotificationCreated;
use App\Models\Notification;

/**
 * Whenever ANY code in the app does `Notification::create([...])`
 * (profile updates, exam published, support ticket resolved, etc.)
 * this observer automatically pushes it live over the websocket
 * to that user's private channel. No controller needs to remember
 * to broadcast manually — it just happens.
 */
class NotificationObserver
{
    public function created(Notification $notification): void
    {
        broadcast(new UserNotificationCreated($notification));
    }
}
