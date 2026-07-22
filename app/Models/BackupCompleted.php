<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackupCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array  $snapshot;
    public float  $storageUsed;
    public string $lastBackupHuman;
    public string $timestamp;

    public function __construct(array $snapshot, float $storageUsed, string $lastBackupHuman)
    {
        $this->snapshot        = $snapshot;
        $this->storageUsed     = $storageUsed;
        $this->lastBackupHuman = $lastBackupHuman;
        $this->timestamp       = now()->toDateTimeString();
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('backups.superadmin')];
    }

    public function broadcastAs(): string
    {
        return 'backup.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'snapshot'          => $this->snapshot,
            'storage_used'      => $this->storageUsed,
            'last_backup_human' => $this->lastBackupHuman,
            'timestamp'         => $this->timestamp,
        ];
    }
}