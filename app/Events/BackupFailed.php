<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackupFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $errorMessage;
    public string $timestamp;

    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        $this->timestamp    = now()->toDateTimeString();
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('backups.superadmin')];
    }

    public function broadcastAs(): string
    {
        return 'backup.failed';
    }

    public function broadcastWith(): array
    {
        return [
            'error_message' => $this->errorMessage,
            'timestamp'     => $this->timestamp,
        ];
    }
}