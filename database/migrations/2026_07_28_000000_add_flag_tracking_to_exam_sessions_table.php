<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Why this migration exists:
 *
 * Every "flag rate" / "flagged sessions" number across the Super Admin
 * Exams Oversight page, the Live Monitoring proctor cards, and the
 * dashboard flag-rate widget was silently always 0 — every one of those
 * queries filters `exam_sessions.is_flagged = true`, but that column has
 * never existed on this table (only a JSON `flags` column does). MySQL
 * throws an "Unknown column" error the moment that query runs, and every
 * call site wraps it in a try/catch that swallows the exception and
 * returns 0. This adds the missing, real columns so those numbers can
 * finally reflect actual data instead of a caught exception.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_sessions', 'flag_count')) {
                $table->unsignedInteger('flag_count')->default(0)->after('flags');
            }
            if (!Schema::hasColumn('exam_sessions', 'is_flagged')) {
                $table->boolean('is_flagged')->default(false)->after('flag_count');
            }
        });

        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->index(['exam_id', 'status'], 'exam_sessions_exam_status_idx');
            $table->index(['user_id', 'exam_id'], 'exam_sessions_user_exam_idx');
        });
    }

    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropIndex('exam_sessions_exam_status_idx');
            $table->dropIndex('exam_sessions_user_exam_idx');
            $table->dropColumn(['flag_count', 'is_flagged']);
        });
    }
};
