<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired the instant a Notification row is written to the database.
 * Pushes the notification straight to the owning user's private channel
 * so the bell/drawer on Dashboard, History, Exams, Settings and Support
 * update live, with no page refresh needed.
 */
class UserNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Notification $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Private per-user channel. Only the owning user can subscribe
     * (see routes/channels.php for the authorization check).
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->notification->user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NotificationCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->notification->id,
            'title'      => $this->notification->title,
            'body'       => $this->notification->body,
            'type'       => $this->notification->type,
            'data'       => $this->notification->data,
            'read'       => false,
            'time'       => $this->notification->created_at?->diffForHumans() ?? 'just now',
            'created_at' => $this->notification->created_at?->toIso8601String(),
        ];
    }
}
