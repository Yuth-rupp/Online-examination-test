<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackupStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public string $triggeredBy;
    public string $timestamp;
    public string $type;

    public function __construct(string $triggeredBy, string $type = 'manual')
    {
        $this->message     = 'Database backup initiated';
        $this->triggeredBy = $triggeredBy;
        $this->type        = $type;
        $this->timestamp   = now()->toDateTimeString();
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('backups.superadmin')];
    }

    public function broadcastAs(): string
    {
        return 'backup.started';
    }

    public function broadcastWith(): array
    {
        return [
            'message'      => $this->message,
            'triggered_by' => $this->triggeredBy,
            'type'         => $this->type,
            'timestamp'    => $this->timestamp,
        ];
    }
}