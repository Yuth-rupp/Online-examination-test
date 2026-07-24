<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * ONE-TIME DATA FIX.
 *
 * `is_passed` on submissions defaulted to `false` and was never actually
 * set by GradingController::store() until this fix — so every submission
 * graded before today has an incorrect `is_passed = 0`, regardless of the
 * student's real score. This recalculates it for every already-graded
 * submission using each submission's own exam's real pass_mark, so the
 * Dashboard and Analytics pass-rate figures become accurate immediately
 * instead of waiting for every submission to be re-graded by hand.
 *
 * Safe to run more than once — it only recomputes from stored data and
 * doesn't touch anything else.
 */
return new class extends Migration
{
    public function up(): void
    {
        $submissions = DB::table('submissions')
            ->join('exams', 'exams.exam_id', '=', 'submissions.exam_id')
            ->where('submissions.status', 'graded')
            ->select('submissions.id', 'submissions.percentage', 'exams.pass_mark')
            ->get();

        foreach ($submissions as $submission) {
            $passMark = $submission->pass_mark ?? 50;
            $isPassed = ((float) $submission->percentage) >= ((float) $passMark);

            DB::table('submissions')
                ->where('id', $submission->id)
                ->update(['is_passed' => $isPassed]);
        }
    }

    public function down(): void
    {
        // Not reversible — the previous state (is_passed always false)
        // was the bug, so there's nothing meaningful to roll back to.
    }
};
