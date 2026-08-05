<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * audit_logs.model_id was created as unsignedBigInteger, but several
 * write paths — most importantly CreateBackupJob and RestoreDatabaseJob —
 * store non-numeric identifiers in it, e.g. "SNAP-2026-08-05-042953".
 *
 * MySQL rejects those inserts outright (a string can't go into a bigint
 * column). AuditLog::create() wraps every write in a try/catch that
 * silently swallows the failure, so nothing ever appeared to go wrong —
 * except that no backup snapshot ever actually got recorded to the audit
 * trail. The Database & Backup page rebuilds its snapshot list from
 * audit_logs on every full page load, so the moment you refreshed, the
 * (never-written) snapshot vanished from the list — even though the
 * underlying .sql file was still sitting safely on the backups disk.
 *
 * Widening the column to a string is backward compatible: every other
 * place that reads model_id (e.g. TeacherController's
 * whereIn('audit_logs.model_id', $teacherExamIds) against numeric exam
 * IDs) keeps working, because MySQL compares a numeric-looking VARCHAR
 * against an INT the same way it always did.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            return;
        }

        // Raw SQL instead of Schema::table(...)->change() so this doesn't
        // depend on doctrine/dbal being installed.
        DB::statement('ALTER TABLE audit_logs MODIFY model_id VARCHAR(191) NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            return;
        }

        // Best-effort revert. Any row that now holds a non-numeric
        // model_id (e.g. a real "SNAP-..." snapshot id written after this
        // migration ran) cannot be safely converted back to bigint, so we
        // null those out rather than fail the rollback outright.
        DB::statement("UPDATE audit_logs SET model_id = NULL WHERE model_id IS NOT NULL AND model_id NOT REGEXP '^[0-9]+$'");
        DB::statement('ALTER TABLE audit_logs MODIFY model_id BIGINT UNSIGNED NULL');
    }
};
