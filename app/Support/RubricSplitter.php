<?php

namespace App\Support;

/**
 * RubricSplitter
 * ─────────────────────────────────────────────────────────────────────
 * PROBLEM THIS FIXES:
 * The essay grading rubric (Accuracy / Depth / Clarity sliders) used to
 * have a HARDCODED max of 10 + 10 + 5 = 25, no matter what the teacher
 * actually configured the essay question's points to be. So a 5-point
 * essay question would still show sliders that went up to 25 — meaning
 * the rubric sub-total the teacher saw ("5 / 25") never matched what
 * the exam was actually configured to award, and the final saved score
 * would be wrong relative to the exam's real total.
 *
 * FIX: Both the grading screen (evaluate.blade.php) and the backend
 * validation (GradingController::store) now derive the three slider
 * maximums FROM the exam's real configured essay points, using the
 * exact same split logic below — so the UI and the server can never
 * disagree, and the numbers always add up to the real max.
 * ─────────────────────────────────────────────────────────────────────
 */
class RubricSplitter
{
    /**
     * Split a total essay point value into [accuracy, depth, clarity]
     * sub-maximums that always sum back up to exactly $essayMaxPts.
     *
     * Weighting: Accuracy 40% / Depth 40% / Clarity 20% — a standard
     * content-vs-polish rubric split. Whatever rounding leaves over is
     * folded into Depth so the three numbers always sum exactly.
     *
     * @param  int  $essayMaxPts  Real configured point value for the essay question(s)
     * @return array{accuracy:int, depth:int, clarity:int, total:int}
     */
    public static function split(int $essayMaxPts): array
    {
        $essayMaxPts = max(0, $essayMaxPts);

        if ($essayMaxPts <= 0) {
            return ['accuracy' => 0, 'depth' => 0, 'clarity' => 0, 'total' => 0];
        }

        if ($essayMaxPts === 1) {
            return ['accuracy' => 1, 'depth' => 0, 'clarity' => 0, 'total' => 1];
        }

        if ($essayMaxPts === 2) {
            return ['accuracy' => 1, 'depth' => 1, 'clarity' => 0, 'total' => 2];
        }

        // Clarity gets ~20% (min 1 once we're past the tiny cases above).
        $clarity = max(1, (int) round($essayMaxPts * 0.2));

        // Accuracy gets ~40% of what's left after Clarity.
        $accuracy = (int) round(($essayMaxPts - $clarity) / 2);

        // Depth absorbs the remainder so accuracy + depth + clarity === $essayMaxPts exactly.
        $depth = $essayMaxPts - $clarity - $accuracy;

        return [
            'accuracy' => $accuracy,
            'depth'    => $depth,
            'clarity'  => $clarity,
            'total'    => $accuracy + $depth + $clarity,
        ];
    }
}
