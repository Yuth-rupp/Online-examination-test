<?php

namespace App\Http\Middleware;

use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Safety net for the Forensic Audit Trail.
 *
 * Individual controllers call AuditLogger::record(...) to write rich,
 * human-readable entries ("department.create", "backup.restore.triggered",
 * etc). This middleware exists so that if a developer ever adds a new
 * Super Admin action and forgets to instrument it, it still gets captured —
 * "detect and record ALL Super Admin activity" doesn't depend on anyone
 * remembering.
 *
 * It runs on every request in the super-admin route group and, after the
 * response is sent, records a generic fallback entry for any state-changing
 * request (POST/PUT/PATCH/DELETE) that didn't already get an explicit,
 * detailed AuditLogger::record() call during its lifecycle.
 */
class CaptureSuperAdminActivity
{
    private const MUTATING_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (!in_array($request->method(), self::MUTATING_METHODS, true)) {
            return;
        }

        // A controller already recorded a detailed entry for this request —
        // don't write a duplicate, less-informative one on top of it.
        if ($request->attributes->get('audit_logged')) {
            return;
        }

        $user = $request->user();
        if (!$user || $user->role !== 'super_admin') {
            return;
        }

        AuditLogger::record(
            action: 'route:' . ($request->route()?->getName() ?? $request->path()),
            modelType: 'HTTP_REQUEST',
            modelId: null,
            payload: [
                'method' => $request->method(),
                'path'   => $request->path(),
                'status' => $response->getStatusCode(),
            ]
        );
    }
}
