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
        $backupDir  = storage_path('app/backups');

        // Ensure backup directory exists
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        try {
            // ── Step 1: Broadcast start ──
            broadcast(new BackupStarted($this->triggeredBy, $this->type));

            // ── Step 2: Perform database dump ──
            $this->performDatabaseDump($filepath);

            // ── Step 3: Calculate file size ──
            $sizeMb = 0;
            if (file_exists($filepath)) {
                $sizeMb = round(filesize($filepath) / 1024 / 1024, 2);
            }

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
            // Clean up partial file
            if (file_exists($filepath)) {
                @unlink($filepath);
            }

            // Log failure
            $this->logAction('backup.failed', 'DATABASE_BACKUP', $snapshotId);

            broadcast(new BackupFailed('Backup failed: ' . $e->getMessage()));

            throw $e; // Let the queue system handle retry/failure
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

        if ($returnCode !== 0) {
            throw new \RuntimeException('mysqldump failed (code ' . $returnCode . '): ' . implode("\n", $output));
        }

        if (!file_exists($filepath) || filesize($filepath) === 0) {
            throw new \RuntimeException('mysqldump produced an empty file.');
        }
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
            // Silently fail if audit_logs table doesn't exist
        }
    }
}