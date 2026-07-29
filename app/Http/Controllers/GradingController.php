<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Exam;
use App\Models\Notification;
use App\Support\RubricSplitter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GradingController extends Controller
{
    /**
     * Show the Master Queue overview page.
     */
    public function queueIndex(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        // Only exams THIS teacher created — otherwise every teacher account
        // (including a brand-new one) sees every submission in the system.
        $teacherExamIds = Exam::where('created_by', $user->user_id)->pluck('exam_id');

        // FIX: also eager-load exam.questions so the queue can show each
        // submission's REAL max points instead of a hardcoded "/40".
        $submissions = Submission::with(['student', 'exam.course', 'exam.questions'])
            ->whereIn('exam_id', $teacherExamIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.grading_queue', compact('submissions'));
    }

    /**
     * JSON endpoint the Grading Queue page polls (and refetches instantly on
     * an 'examsystem:live-update' push) so newly-submitted papers and freshly
     * saved grades — including the Avg Score card — show up without a manual
     * page refresh, the same "LIVE" pattern used by the Analytics page.
     */
    public function queueLiveData(Request $request)
    {
        $user = $request->user() ?? Auth::user();
        $teacherExamIds = Exam::where('created_by', $user->user_id)->pluck('exam_id');

        $submissions = Submission::with(['student', 'exam.course', 'exam.questions'])
            ->whereIn('exam_id', $teacherExamIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'submissions'  => $submissions->map(fn ($sub) => $this->serializeSubmissionForQueue($sub))->values(),
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Shape a submission for the Grading Queue's Alpine dataset.
     *
     * FIX: total_max is each EXAM's real sum of question points (or the
     * exam's own total_marks/max_score, or a per-question default) instead
     * of a hardcoded 40 — a teacher whose exam is out of 25, 50, or 100
     * points was previously always compared against "/ 40".
     */
    private function serializeSubmissionForQueue(Submission $sub): array
    {
        $exam = $sub->exam;
        $questions = $exam?->questions ?? collect();

        $totalMax = $questions->sum('points')
            ?: $exam?->total_marks
            ?: $exam?->max_score
            ?: ($questions->count() * 5)
            ?: 100;

        return [
            'id'               => $sub->id,
            'student_name'     => $sub->student->full_name ?? 'Unknown Student',
            'institutional_id' => $sub->student->institutional_id ?? '—',
            'subject_title'    => $exam?->title ?? 'Untitled Exam',
            'course_code'      => $exam?->course?->code ?? '—',
            'clean_exam_id'    => $sub->exam_id ? substr($sub->exam_id, 0, 8) : '—',
            'status'           => $sub->status,
            'total_score'      => (float) ($sub->total_score ?? 0),
            'total_max'        => (float) $totalMax,
        ];
    }

    /**
     * Display the individual student evaluation paper workspace.
     *
     * FIX: Now loads submission_answers so the blade can show what
     *      the student selected vs the correct answer.
     */
    public function show($id)
    {
        $user = Auth::user();
        $teacherExamIds = Exam::where('created_by', $user->user_id)->pluck('exam_id');

        $submission = Submission::with(['student', 'exam.questions', 'exam.course'])
            ->where('id', $id)
            ->whereIn('exam_id', $teacherExamIds)
            ->firstOrFail();

        // ✅ FIX: Load student's saved answers keyed by question_id
        //    Without this, the blade has no data → shows "Not answered" for everything.
        $submissionAnswers = DB::table('submission_answers')
            ->where('submission_id', $submission->id)
            ->pluck('answer_text', 'question_id');
        //  $submissionAnswers is now a Collection like:
        //  { "5": "A", "6": "TRUE", "7": "Some essay text..." }

        // ✅ FIX: submission_answers.question_id has no FK constraint, and a
        // question can be edited/reassigned to a different exam (or deleted)
        // in the Question Bank *after* a student has already answered it.
        // When that happens, $submission->exam->questions() no longer
        // includes it and the grading screen showed "No questions found"
        // even though the student's answers were still on record. Recover
        // any answered questions that have since fallen out of the exam's
        // live question list, so grading always reflects what the student
        // actually answered, not just the bank's current state.
        $this->recoverDetachedQuestions($submission, $submissionAnswers);

        // FIX: scope prev/next to this teacher's own exams — the old query
        // walked every submission id in the whole database, which could
        // land on another teacher's paper (or fail the ->whereIn() guard
        // entirely) when this teacher's ids weren't sequential.
        $prev = Submission::whereIn('exam_id', $teacherExamIds)
            ->where('id', '<', $submission->id)->orderBy('id', 'desc')->first();
        $next = Submission::whereIn('exam_id', $teacherExamIds)
            ->where('id', '>', $submission->id)->orderBy('id', 'asc')->first();

        return view('teacher.grading.evaluate',
            compact('submission', 'prev', 'next', 'submissionAnswers'));
    }

    /**
     * Recover any answered questions that have fallen out of the exam's
     * live `questions` relation (edited to a different exam, or otherwise
     * detached) and re-attach them to $submission->exam->questions in
     * memory. Shared by show() (what the teacher sees) and store() (what
     * actually gets saved) so the two can never disagree on the total.
     */
    private function recoverDetachedQuestions(Submission $submission, $submissionAnswers): void
    {
        $answeredQuestionIds = $submissionAnswers->keys()->map(fn ($id) => (int) $id);
        $examQuestionIds = $submission->exam->questions->pluck('id');
        $missingQuestionIds = $answeredQuestionIds->diff($examQuestionIds);

        if ($missingQuestionIds->isEmpty()) {
            return;
        }

        $recoveredQuestions = \App\Models\Question::whereIn('id', $missingQuestionIds)->get();

        if ($recoveredQuestions->isNotEmpty()) {
            $submission->exam->setRelation(
                'questions',
                $submission->exam->questions
                    ->concat($recoveredQuestions)
                    ->sortBy('order')
                    ->values()
            );
        }
    }

    /**
     * Save the evaluated grade scores back to database storage tables.
     */
    public function store(Request $request, $submission_id)
    {
        $submission = Submission::with('exam.questions')->findOrFail($submission_id);

        // ✅ FIX: without this, store() recalculated totals from a fresh,
        // possibly-detached question list while the teacher was looking at
        // the recovered list on screen (see show()/recoverDetachedQuestions).
        // That mismatch meant Save could silently persist a different
        // score/percentage than what was just displayed. Apply the same
        // recovery here so what's saved always matches what was shown.
        $submissionAnswersForRecovery = DB::table('submission_answers')
            ->where('submission_id', $submission->id)
            ->pluck('answer_text', 'question_id');
        $this->recoverDetachedQuestions($submission, $submissionAnswersForRecovery);

        // ── Dynamic rubric max ──────────────────────────────────────────
        // FIX: the rubric used to always validate against a hardcoded
        // 10 / 10 / 5 (25 total) no matter what the essay question was
        // actually configured to be worth. If the teacher's exam had a
        // 5-point essay question, the UI and the server disagreed about
        // what "full marks" meant. Now both sides derive the same three
        // maximums from the exam's real configured essay points via
        // RubricSplitter — so a submitted score can never exceed what the
        // exam is actually worth.
        $questions   = $submission->exam->questions ?? collect();
        $essayQs     = $questions->filter(fn($q) => in_array(strtolower($q->type ?? ''), ['essay', 'text', 'essay/text']));
        $essayMaxPts = $essayQs->sum('points') ?: ($essayQs->count() ? 25 : 0);
        $rubricMax   = RubricSplitter::split((int) $essayMaxPts);

        $validated = $request->validate([
            'accuracy' => "required|integer|min:0|max:{$rubricMax['accuracy']}",
            'depth'    => "required|integer|min:0|max:{$rubricMax['depth']}",
            'clarity'  => "required|integer|min:0|max:{$rubricMax['clarity']}",
            'feedback' => 'nullable|string',
        ]);

        // Read the hidden auto_score calculated inside the view layout context
        $autoScore = (int)$request->input('auto_score', 0);
        
        $manualScore = (int)$validated['accuracy'] + (int)$validated['depth'] + (int)$validated['clarity'];
        $finalScore = $autoScore + $manualScore;

        // Real total possible points for THIS exam — the same calculation
        // used on the grading screen (evaluate.blade.php) — instead of a
        // hardcoded 40 that ignored what the teacher actually set per
        // question. This is why a 20-point exam was showing as "100".
        $questions = $submission->exam->questions ?? collect();
        $totalPts  = $questions->sum('points') ?: ($questions->count() * 5) ?: 40;
        $percentage = min(100, round(($finalScore / $totalPts) * 100, 2));

        // ✅ FIX: is_passed was never set anywhere in the app — it stayed at
        // its DB default of `false` for every graded submission forever.
        // That's why the Dashboard's "Pass Rate" card (and, since it now
        // also reads is_passed, the Analytics pass rate too) always showed
        // close to 0% no matter how students actually scored. Compute it
        // here, against the exam's real configured pass_mark — not a
        // hardcoded 50% — the same threshold now used for the on-screen
        // PASS/FAIL badge in evaluate.blade.php.
        $passMarkPercent = $submission->exam->pass_mark ?? 50;
        $isPassed = $percentage >= $passMarkPercent;

        // Synchronize rubric metrics and save notes directly to feedback column
        $submission->update([
            'accuracy_score'   => $validated['accuracy'],
            'depth_score'      => $validated['depth'],
            'clarity_score'    => $validated['clarity'],
            'total_score'      => $finalScore,
            'percentage'       => $percentage,
            'is_passed'        => $isPassed,
            'status'           => 'graded',
            'teacher_feedback' => $validated['feedback'] ?? null, // Fixed mapping to look up model parameter label configuration exactly
            'graded_by'        => Auth::id(),
            'graded_at'        => now()
        ]);

        // Push a live "Exam Graded" notification to the student. This is what the
        // NotificationObserver picks up and broadcasts over the
        // notifications.{userId} private channel — the same pipe the "Exam
        // Submitted" notification already uses on the student side.
        $examTitle = $submission->exam->title ?? 'your exam';
        Notification::create([
            'user_id' => $submission->user_id,
            'title'   => 'Exam Graded',
            'body'    => "Your exam \"{$examTitle}\" has been graded. You scored {$percentage}%.",
            'type'    => 'success',
            'data'    => [
                'submission_id' => $submission->id,
                'exam_id'       => $submission->exam_id,
                'percentage'    => $percentage,
            ],
        ]);

        // ── Save & Grade Next ────────────────────────────────────────────
        // FIX: this used to redirect with ['id' => ...] but the route is
        // registered as /teacher/grading/evaluate/{student_id} — passing
        // the wrong key meant the URL never resolved correctly and "Save &
        // Grade Next" silently failed to advance. It also picked "next" by
        // raw id across every submission in the database rather than the
        // teacher's own remaining pending-grading queue.
        //
        // Now: jump to the next PENDING submission still waiting in THIS
        // teacher's queue (oldest first, same order the queue page uses).
        // If there isn't one — including the "only one student" case —
        // go back to the queue with a clear "all caught up" message
        // instead of silently doing nothing.
        if ($request->input('action') === 'save_next') {
            $teacherExamIds = Exam::where('created_by', Auth::user()->user_id)->pluck('exam_id');

            $next = Submission::whereIn('exam_id', $teacherExamIds)
                ->where('id', '!=', $submission->id)
                ->where('status', 'pending_grading')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($next) {
                return redirect()
                    ->route('teacher.grading.show', ['student_id' => $next->id])
                    ->with('success', 'Grade saved! Moved to the next student.');
            }

            return redirect()
                ->route('teacher.grading.queue')
                ->with('success', "Grade saved! You're all caught up — no more submissions waiting to be graded.");
        }

        return redirect()->route('teacher.grading.queue')->with('success', 'Grading session updated successfully.');
    }

    /**
     * Success landing after grading milestone completions.
     */
    public function success($submission_id)
    {
        $lastGraded = Submission::with('student', 'exam.course')->findOrFail($submission_id);
        
        $nextStudent = Submission::with('student')
            ->where('exam_id', $lastGraded->exam_id)
            ->where('status', 'pending_grading')
            ->orderBy('id', 'asc')
            ->first();

        $totalStudents = Submission::where('exam_id', $lastGraded->exam_id)->count();
        $completedCount = Submission::where('exam_id', $lastGraded->exam_id)->where('status', 'graded')->count();
        $remainingCount = $totalStudents - $completedCount;
        $progressPercentage = $totalStudents > 0 ? round(($completedCount / $totalStudents) * 100) : 0;

        return view('teacher.grading_confirmation', compact(
            'lastGraded', 'nextStudent', 'totalStudents', 'completedCount', 'remainingCount', 'progressPercentage'
        ));
    }
}