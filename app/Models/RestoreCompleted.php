<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RestoreCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $snapshotId;
    public string $message;
    public string $timestamp;

    public function __construct(string $snapshotId)
    {
        $this->snapshotId = $snapshotId;
        $this->message    = "Database restored to snapshot {$snapshotId}";
        $this->timestamp  = now()->toDateTimeString();
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('backups.superadmin')];
    }

    public function broadcastAs(): string
    {
        return 'restore.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'snapshot_id' => $this->snapshotId,
            'message'     => $this->message,
            'timestamp'   => $this->timestamp,
        ];
    }
}