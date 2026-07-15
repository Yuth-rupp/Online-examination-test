<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Response;

class InfrastructureController extends Controller
{
    public function dashboard()
    {
        return view('superadmin.superadmin_dashboard');
    }

    public function auditLogs()
    {
        return view('superadmin.audit_logs');
    }

    public function backups()
    {
        return view('superadmin.backups');
    }

    public function settings()
    {
        return view('superadmin.settings');
    }

    public function exportAuditLogsCsv()
    {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->get();
        $csvFileName = 'audit_trail_telemetry_' . now()->format('Y_m_d_His') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Timestamp Telemetry', 'Operator', 'Action Signature', 'Resource Type', 'Resource ID', 'Client IP Address']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at,
                    $log->user ? $log->user->full_name : 'System Core',
                    $log->action,
                    $log->resource_type,
                    $log->resource_id,
                    $log->ip_address
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}