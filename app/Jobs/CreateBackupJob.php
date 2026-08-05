<?php

namespace App\Jobs;

use App\Events\BackupStarted;
use App\Events\BackupCompleted;
use App\Events\BackupFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\AuditLog;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int    $tries   = 1;
    public int    $timeout = 300; // 5 minutes max
    public string $triggeredBy;
    public string $type;

    public function __construct(string $triggeredBy, string $type = 'manual')
    {
        $this->triggeredBy = $triggeredBy;
        $this->type        = $type;
    }

    public function handle(): void
    {
        $snapshotId = 'SNAP-' . now()->format('Y-m-d-His');
        $filename   = $snapshotId . '.sql';

        // Database dump CLI tools (mysqldump/pg_dump/sqlite3) can only write to a
        // local path — never straight to S3 — so we always dump to a scratch
        // file first, then push the finished file to the durable 'backups' disk.
        // The scratch file is deleted in finally{} either way.
        $tmpDir  = storage_path('app/tmp-backups');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $filename;

        try {
            // ── Step 1: Broadcast start ──
            broadcast(new BackupStarted($this->triggeredBy, $this->type));

            // ── Step 2: Perform database dump to the local scratch file ──
            $this->performDatabaseDump($tmpPath);

            if (!file_exists($tmpPath) || filesize($tmpPath) === 0) {
                throw new \RuntimeException('Backup produced an empty file.');
            }

            // ── Step 3: Push the finished dump to the durable 'backups' disk ──
            // (S3/Railway Bucket in production — see config/filesystems.php.
            // This is what makes the snapshot survive a container restart.)
            $stream = fopen($tmpPath, 'r');
            $stored = Storage::disk('backups')->put($filename, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            if ($stored === false) {
                throw new \RuntimeException('Failed to persist snapshot to the backups disk.');
            }

            $sizeMb = round(Storage::disk('backups')->size($filename) / 1024 / 1024, 2);

            // ── Step 4: Log to audit trail ──
            $this->logAction(
                $this->type === 'manual' ? 'backup.manual.created' : 'backup.automated.created',
                'DATABASE_BACKUP',
                $snapshotId
            );

            // ── Step 5: Build snapshot data and broadcast completion ──
            $snapshot = [
                'id'         => $snapshotId,
                'created_at' => now()->toDateTimeString(),
                'size_mb'    => $sizeMb,
                'type'       => $this->type,
                'status'     => 'completed',
                'filename'   => $filename,
            ];

            $storageUsed     = $this->getStorageUsedPercent();
            $lastBackupHuman = 'Just now';

            broadcast(new BackupCompleted($snapshot, $storageUsed, $lastBackupHuman));

        } catch (\Throwable $e) {
            // Log failure
            $this->logAction('backup.failed', 'DATABASE_BACKUP', $snapshotId);

            broadcast(new BackupFailed('Backup failed: ' . $e->getMessage()));

            throw $e; // Let the queue system handle retry/failure
        } finally {
            // Always clean up the local scratch file — the durable copy (if any)
            // now lives on the 'backups' disk, not on ephemeral container disk.
            if (file_exists($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }

    /**
     * Perform the actual database dump.
     * Supports MySQL/MariaDB and SQLite.
     */
    private function performDatabaseDump(string $filepath): void
    {
        $driver = config('database.default');
        $config = config("database.connections.{$driver}");

        switch ($driver) {
            case 'mysql':
                $this->dumpMysql($config, $filepath);
                break;

            case 'sqlite':
                $this->dumpSqlite($config, $filepath);
                break;

            case 'pgsql':
                $this->dumpPostgres($config, $filepath);
                break;

            default:
                // Fallback: export all tables as INSERT statements via PHP
                $this->dumpViaPhp($filepath);
                break;
        }
    }

    private function dumpMysql(array $config, string $filepath): void
    {
        // Some production containers (e.g. Railway's Octane/FrankenPHP runtime)
        // don't have the mysqldump CLI on PATH even when the nixpacks build
        // installed it. Rather than hard-failing the whole backup, detect that
        // up front and fall back to the pure-PHP dumper, which needs no binary.
        if (!$this->commandExists('mysqldump')) {
            $this->dumpViaPhp($filepath);
            return;
        }

        $host     = $config['host'] ?? '127.0.0.1';
        $port     = $config['port'] ?? 3306;
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'] ?? '';

        // Build mysqldump command
        $cmd = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s %s --single-transaction --routines --triggers > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        exec($cmd, $output, $returnCode);

        // Even if commandExists() passed, still fall back on a 127 (not
        // found) or 126 (found but not executable) instead of failing the
        // whole backup — belt and braces for flaky container environments.
        if (in_array($returnCode, [126, 127], true)) {
            $this->dumpViaPhp($filepath);
            return;
        }

        if ($returnCode !== 0) {
            throw new \RuntimeException('mysqldump failed (code ' . $returnCode . '): ' . implode("\n", $output));
        }

        if (!file_exists($filepath) || filesize($filepath) === 0) {
            throw new \RuntimeException('mysqldump produced an empty file.');
        }
    }

    /**
     * Check whether a CLI binary is available on PATH before we try to shell
     * out to it. Cheap way to avoid a guaranteed exit-127 exec() call.
     */
    private function commandExists(string $binary): bool
    {
        $which = stripos(PHP_OS, 'WIN') === 0 ? 'where' : 'which';
        exec(sprintf('%s %s 2>/dev/null', $which, escapeshellarg($binary)), $output, $code);
        return $code === 0 && !empty($output);
    }

    private function dumpSqlite(array $config, string $filepath): void
    {
        $dbPath = $config['database'];

        if (!file_exists($dbPath)) {
            throw new \RuntimeException('SQLite database file not found: ' . $dbPath);
        }

        // For SQLite, we can simply copy the database file, or use .dump
        $cmd = sprintf('sqlite3 %s .dump > %s 2>&1', escapeshellarg($dbPath), escapeshellarg($filepath));
        exec($cmd, $output, $returnCode);

        // Fallback: just copy the file if sqlite3 CLI is not available
        if ($returnCode !== 0) {
            copy($dbPath, $filepath);
        }
    }

    private function dumpPostgres(array $config, string $filepath): void
    {
        $host     = $config['host'] ?? '127.0.0.1';
        $port     = $config['port'] ?? 5432;
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'] ?? '';

        // Set password via environment variable
        $envPrefix = $password ? 'PGPASSWORD=' . escapeshellarg($password) . ' ' : '';

        $cmd = sprintf(
            '%spg_dump --host=%s --port=%s --username=%s --format=plain --no-owner --no-acl %s > %s 2>&1',
            $envPrefix,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('pg_dump failed (code ' . $returnCode . '): ' . implode("\n", $output));
        }
    }

    /**
     * Fallback dump using pure PHP — works with any database driver.
     */
    private function dumpViaPhp(string $filepath): void
    {
        $tables = DB::select('SHOW TABLES');
        $sql    = "-- ExamSystem Database Backup\n-- Generated: " . now()->toDateTimeString() . "\n\n";

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];

            // Get CREATE TABLE statement
            $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($create)) {
                $createSql = $create[0]->{'Create Table'} ?? '';
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n{$createSql};\n\n";
            }

            // Get all rows
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = collect((array) $row)->map(function ($v) {
                    return $v === null ? 'NULL' : "'" . addslashes($v) . "'";
                })->implode(', ');
                $sql .= "INSERT INTO `{$tableName}` VALUES ({$values});\n";
            }
            $sql .= "\n";
        }

        file_put_contents($filepath, $sql);
    }

    private function getStorageUsedPercent(): float
    {
        try {
            $total = disk_total_space(base_path());
            $free  = disk_free_space(base_path());
            return $total > 0 ? round((($total - $free) / $total) * 100, 1) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function logAction(string $action, string $modelType, string $modelId): void
    {
        try {
            AuditLog::create([
                'user_id'        => null, // Job context — no auth user
                'institution_id' => null,
                'action'         => $action,
                'model_type'     => $modelType,
                'model_id'       => $modelId,
                'ip_address'     => 'queue-worker',
                'created_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // Was previously a bare silent catch — that's exactly how the
            // model_id VARCHAR/BIGINT mismatch went unnoticed for so long.
            // A backup can still be considered successful even if this one
            // write fails, so we don't rethrow, but it must show up in the
            // logs instead of disappearing without a trace.
            \Illuminate\Support\Facades\Log::warning(
                'CreateBackupJob: failed to write audit log entry for "' . $action . '": ' . $e->getMessage()
            );
        }
    }
}