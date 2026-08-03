<?php

namespace App\Jobs;

use App\Events\RestoreStarted;
use App\Events\RestoreCompleted;
use App\Events\RestoreFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\AuditLog;

class RestoreDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int    $tries   = 1;
    public int    $timeout = 600; // 10 minutes max
    public string $snapshotId;
    public string $triggeredBy;

    public function __construct(string $snapshotId, string $triggeredBy)
    {
        $this->snapshotId  = $snapshotId;
        $this->triggeredBy = $triggeredBy;
    }

    public function handle(): void
    {
        $filename = $this->snapshotId . '.sql';

        // Restore CLI tools (mysql/psql/sqlite3) need a local file to read from,
        // so we pull the snapshot down from the durable 'backups' disk
        // (S3/Railway Bucket in prod) into a scratch file first.
        $tmpDir = storage_path('app/tmp-backups');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        $filepath = $tmpDir . DIRECTORY_SEPARATOR . $filename;

        try {
            // ── Step 1: Broadcast start ──
            broadcast(new RestoreStarted($this->snapshotId, $this->triggeredBy));

            // ── Step 2: Validate the snapshot actually exists on the backups disk ──
            if (!Storage::disk('backups')->exists($filename)) {
                throw new \RuntimeException("Snapshot not found on the backups disk: {$filename}");
            }

            // ── Step 3: Download it to the local scratch path ──
            $stream = Storage::disk('backups')->readStream($filename);
            if ($stream === null) {
                throw new \RuntimeException("Failed to read snapshot from the backups disk: {$filename}");
            }
            file_put_contents($filepath, stream_get_contents($stream));
            fclose($stream);

            if (!file_exists($filepath) || filesize($filepath) === 0) {
                throw new \RuntimeException("Downloaded snapshot is empty: {$filename}");
            }

            // ── Step 4: Perform restore against the local scratch file ──
            $this->performRestore($filepath);

            // ── Step 5: Log to audit trail ──
            $this->logAction('backup.restore.completed', 'DATABASE_RESTORE', $this->snapshotId);

            // ── Step 6: Broadcast completion ──
            broadcast(new RestoreCompleted($this->snapshotId));

        } catch (\Throwable $e) {
            $this->logAction('backup.restore.failed', 'DATABASE_RESTORE', $this->snapshotId);
            broadcast(new RestoreFailed($this->snapshotId, 'Restoration failed: ' . $e->getMessage()));
            throw $e;
        } finally {
            // Always clean up the scratch copy — the source of truth stays on
            // the 'backups' disk.
            if (file_exists($filepath)) {
                @unlink($filepath);
            }
        }
    }

    private function performRestore(string $filepath): void
    {
        $driver = config('database.default');
        $config = config("database.connections.{$driver}");

        switch ($driver) {
            case 'mysql':
                $this->restoreMysql($config, $filepath);
                break;

            case 'sqlite':
                $this->restoreSqlite($config, $filepath);
                break;

            case 'pgsql':
                $this->restorePostgres($config, $filepath);
                break;

            default:
                $this->restoreViaPhp($filepath);
                break;
        }
    }

    private function restoreMysql(array $config, string $filepath): void
    {
        $host     = $config['host'] ?? '127.0.0.1';
        $port     = $config['port'] ?? 3306;
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'] ?? '';

        $cmd = sprintf(
            'mysql --host=%s --port=%s --user=%s %s %s < %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('MySQL restore failed (code ' . $returnCode . '): ' . implode("\n", $output));
        }
    }

    private function restoreSqlite(array $config, string $filepath): void
    {
        $dbPath = $config['database'];

        // Create a backup of the current database before restoring
        $safetyBackup = $dbPath . '.pre-restore-' . time();
        if (file_exists($dbPath)) {
            copy($dbPath, $safetyBackup);
        }

        // Check if the backup is a raw copy or a .dump file
        $content = file_get_contents($filepath, false, null, 0, 100);

        if (str_starts_with(trim($content), 'SQLite format')) {
            // Raw SQLite file — just copy it
            copy($filepath, $dbPath);
        } else {
            // SQL dump file — execute it
            $cmd = sprintf('sqlite3 %s < %s 2>&1', escapeshellarg($dbPath), escapeshellarg($filepath));
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0) {
                // Revert to safety backup
                if (file_exists($safetyBackup)) {
                    copy($safetyBackup, $dbPath);
                }
                throw new \RuntimeException('SQLite restore failed: ' . implode("\n", $output));
            }
        }

        // Clean up safety backup on success
        if (file_exists($safetyBackup)) {
            @unlink($safetyBackup);
        }
    }

    private function restorePostgres(array $config, string $filepath): void
    {
        $host     = $config['host'] ?? '127.0.0.1';
        $port     = $config['port'] ?? 5432;
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'] ?? '';

        $envPrefix = $password ? 'PGPASSWORD=' . escapeshellarg($password) . ' ' : '';

        $cmd = sprintf(
            '%spsql --host=%s --port=%s --username=%s --dbname=%s --file=%s 2>&1',
            $envPrefix,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('PostgreSQL restore failed (code ' . $returnCode . '): ' . implode("\n", $output));
        }
    }

    private function restoreViaPhp(string $filepath): void
    {
        $sql = file_get_contents($filepath);

        if (empty($sql)) {
            throw new \RuntimeException('Backup file is empty.');
        }

        // Split by statement and execute
        $statements = array_filter(
            array_map('trim', explode(";\n", $sql)),
            fn($s) => !empty($s) && !str_starts_with($s, '--')
        );

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($statements as $statement) {
            try {
                DB::unprepared($statement . ';');
            } catch (\Exception $e) {
                // Log but continue — some statements may fail on re-run
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function logAction(string $action, string $modelType, string $modelId): void
    {
        try {
            AuditLog::create([
                'user_id'        => null,
                'institution_id' => null,
                'action'         => $action,
                'model_type'     => $modelType,
                'model_id'       => $modelId,
                'ip_address'     => 'queue-worker',
                'created_at'     => now(),
            ]);
        } catch (\Exception $e) {}
    }
}