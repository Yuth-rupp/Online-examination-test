<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RestoreFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $snapshotId;
    public string $errorMessage;
    public string $timestamp;

    public function __construct(string $snapshotId, string $errorMessage)
    {
        $this->snapshotId   = $snapshotId;
        $this->errorMessage = $errorMessage;
        $this->timestamp    = now()->toDateTimeString();
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('backups.superadmin')];
    }

    public function broadcastAs(): string
    {
        return 'restore.failed';
    }

    public function broadcastWith(): array
    {
        return [
            'snapshot_id'   => $this->snapshotId,
            'error_message' => $this->errorMessage,
            'timestamp'     => $this->timestamp,
        ];
    }
}