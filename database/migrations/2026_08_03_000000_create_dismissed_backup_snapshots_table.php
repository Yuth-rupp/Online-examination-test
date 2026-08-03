<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Some "snapshot" rows on the Database & Backup page are reconstructed
     * purely from audit_logs when the real .sql file is missing from the
     * 'backups' disk (e.g. it was written to Railway's ephemeral local disk
     * before R2 was configured, and lost on a container restart). These
     * rows have no file to restore or delete.
     *
     * The audit trail itself must stay untouched (append-only, forensic),
     * but the Super Admin still needs a way to clear these fileless rows
     * out of the snapshot list. This table records which ones have been
     * dismissed so they stop being reconstructed into the list — without
     * ever deleting the underlying audit_logs rows.
     */
    public function up(): void
    {
        Schema::create('dismissed_backup_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('snapshot_id')->unique();
            $table->foreignId('dismissed_by')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->timestamp('dismissed_at');

            $table->index('snapshot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dismissed_backup_snapshots');
    }
};
