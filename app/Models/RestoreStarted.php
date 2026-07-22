<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RestoreStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $snapshotId;
    public string $triggeredBy;
    public string $timestamp;

    public function __construct(string $snapshotId, string $triggeredBy)
    {
        $this->snapshotId  = $snapshotId;
        $this->triggeredBy = $triggeredBy;
        $this->timestamp   = now()->toDateTimeString();
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('backups.superadmin')];
    }

    public function broadcastAs(): string
    {
        return 'restore.started';
    }

    public function broadcastWith(): array
    {
        return [
            'snapshot_id'  => $this->snapshotId,
            'triggered_by' => $this->triggeredBy,
            'timestamp'    => $this->timestamp,
        ];
    }
}