<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Single write path for every audit_logs row in the app.
 *
 * Why this exists: before this, every controller inserted into
 * audit_logs directly with `DB::table('audit_logs')->insert([...])`.
 * That bypassed Eloquent entirely, so nothing could hook into "an audit
 * event just happened" in one place — real-time broadcasting had to be
 * bolted onto each call site individually (and inevitably some were
 * missed).
 *
 * Routing every write through `AuditLog::create()` here means:
 *   1. AuditLogObserver fires every time, so every recorded action is
 *      pushed live to the Super Admin audit page automatically.
 *   2. The current request is flagged as "already audited", so the
 *      CaptureSuperAdminActivity catch-all middleware (which exists to
 *      guarantee NOTHING a Super Admin does goes unrecorded) knows not
 *      to write a second, less detailed, duplicate entry for the same
 *      request.
 */
class AuditLogger
{
    public static function record(
        string $action,
        ?string $modelType = null,
        $modelId = null,
        ?array $payload = null,
        $institutionId = null
    ): ?AuditLog {
        try {
            $log = AuditLog::create([
                'user_id'        => Auth::id(),
                'institution_id' => $institutionId ?? (Auth::user()->institution_id ?? null),
                'action'         => $action,
                'model_type'     => $modelType,
                'model_id'       => $modelId,
                'payload'        => $payload,
                'ip_address'     => request()->ip(),
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('AuditLogger::record failed: ' . $e->getMessage());
            $log = null;
        }

        // Mark this request as already audited so the catch-all middleware
        // doesn't also log a generic fallback entry for it.
        if (request()) {
            request()->attributes->set('audit_logged', true);
        }

        return $log;
    }
}
