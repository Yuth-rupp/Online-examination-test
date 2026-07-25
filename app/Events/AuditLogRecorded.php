<?php

namespace App\Events;

use App\Models\AuditLog;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired the instant a row is written to audit_logs — no matter which
 * controller, service, or middleware wrote it. Pushes the event straight
 * to the Super Admin "Forensic Audit Trails" page so new entries appear
 * the moment they happen, with no page refresh and no waiting on the
 * polling loop.
 *
 * Mirrors App\Events\UserNotificationCreated (same broadcast-on-create
 * pattern, driven by App\Observers\AuditLogObserver).
 */
class AuditLogRecorded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AuditLog $auditLog;

    public function __construct(AuditLog $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    /**
     * Private channel, gated to super_admin only — see routes/channels.php.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('audit-logs.superadmin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'AuditLogRecorded';
    }

    /**
     * Shaped identically to the rows returned by
     * SuperAdminController::auditLogsApi() so the front-end can push this
     * straight into the same render path the polling fallback already uses.
     */
    public function broadcastWith(): array
    {
        $log  = $this->auditLog;
        $user = $log->user; // lazy-loaded on purpose — this fires rarely enough that the extra query is cheap.

        return [
            'id'         => $log->id,
            'operator'   => $user ? $user->full_name : 'System',
            'role'       => $user ? $user->role : 'system',
            'action'     => $log->action ?? '—',
            'resource'   => ($log->model_type ?? 'SYSTEM') . ($log->model_id ? ' [ID: ' . $log->model_id . ']' : ''),
            'ip'         => $log->ip_address ?? '—',
            'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
        ];
    }
}
