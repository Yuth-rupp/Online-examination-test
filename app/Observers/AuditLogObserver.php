<?php

namespace App\Observers;

use App\Events\AuditLogRecorded;
use App\Models\AuditLog;

/**
 * Whenever ANY code in the app does `AuditLog::create([...])`
 * (Super Admin actions, department/institution changes, the
 * catch-all CaptureSuperAdminActivity middleware, etc.) this
 * observer automatically pushes it live over the websocket to
 * the Forensic Audit Trails page. No controller needs to remember
 * to broadcast manually — it just happens.
 */
class AuditLogObserver
{
    public function created(AuditLog $auditLog): void
    {
        broadcast(new AuditLogRecorded($auditLog));
    }
}
