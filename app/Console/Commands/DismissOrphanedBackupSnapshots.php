<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DismissOrphanedBackupSnapshots extends Command
{
    /**
     * php artisan backups:dismiss-orphans
     * php artisan backups:dismiss-orphans --dry-run
     */
    protected $signature = 'backups:dismiss-orphans {--dry-run : Show what would be dismissed without writing anything}';

    protected $description = 'Auto-dismiss backup snapshot rows whose .sql file no longer exists on the backups disk, so the Database & Backup page stays clean.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $alreadyDismissed = DB::table('dismissed_backup_snapshots')
            ->pluck('snapshot_id')
            ->all();

        $logs = DB::table('audit_logs')
            ->where('action', 'like', '%backup%')
            ->where('action', 'not like', '%triggered%')
            ->orderBy('created_at', 'desc')
            ->take(200)
            ->get();

        if ($logs->isEmpty()) {
            $this->info('No backup-related audit log entries found. Nothing to do.');
            return self::SUCCESS;
        }

        $toDismiss = [];

        foreach ($logs as $log) {
            $basename = $log->model_id ?: ('SNAP-' . Carbon::parse($log->created_at)->format('Y-m-d-His'));

            if (in_array($basename, $alreadyDismissed, true)) {
                continue;
            }

            $filename = $basename . '.sql';
            $hasFile  = false;

            try {
                $hasFile = Storage::disk('backups')->exists($filename);
            } catch (\Throwable $e) {
                // Disk unreachable — treat as fileless, same behaviour as the
                // controller's dismiss/list logic.
                $hasFile = false;
            }

            if (!$hasFile) {
                $toDismiss[$basename] = true; // dedupe identical basenames
            }
        }

        if (empty($toDismiss)) {
            $this->info('No orphaned (fileless) snapshot rows found. Nothing to dismiss.');
            return self::SUCCESS;
        }

        $this->info(count($toDismiss) . ' orphaned snapshot(s) found:');
        foreach (array_keys($toDismiss) as $id) {
            $this->line(" - {$id}");
        }

        if ($dryRun) {
            $this->comment('Dry run — nothing was written. Re-run without --dry-run to apply.');
            return self::SUCCESS;
        }

        $now = now();
        $rows = array_map(fn ($id) => [
            'snapshot_id'   => $id,
            'dismissed_by'  => null, // system-triggered, not a specific admin
            'dismissed_at'  => $now,
        ], array_keys($toDismiss));

        DB::table('dismissed_backup_snapshots')->upsert(
            $rows,
            ['snapshot_id'],
            ['dismissed_by', 'dismissed_at']
        );

        try {
            AuditLog::create([
                'user_id'        => null,
                'institution_id' => null,
                'action'         => 'backup.snapshot.auto_dismissed',
                'model_type'     => 'DATABASE_BACKUP',
                'model_id'       => implode(',', array_slice(array_keys($toDismiss), 0, 20)),
                'ip_address'     => 'artisan-command',
                'created_at'     => $now,
            ]);
        } catch (\Throwable $e) {
            $this->warn('Dismissed rows saved, but failed to write the summary audit log entry: ' . $e->getMessage());
        }

        $this->info('Done. ' . count($toDismiss) . ' snapshot(s) dismissed.');
        return self::SUCCESS;
    }
}