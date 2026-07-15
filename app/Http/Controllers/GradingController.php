<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GradingController extends Controller
{
    /**
     * Show the Master Queue overview page.
     */
    public function queueIndex(Request $request)
    {
        $submissions = Submission::with(['student', 'exam.course'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.grading_queue', compact('submissions'));
    }

    /**
     * Display the individual student evaluation paper workspace.
     *
     * FIX: Now loads submission_answers so the blade can show what
     *      the student selected vs the correct answer.
     */
    public function show($id)
    {
        $submission = Submission::with(['student', 'exam.questions', 'exam.course'])
            ->where('id', $id)
            ->firstOrFail();

        // ✅ FIX: Load student's saved answers keyed by question_id
        //    Without this, the blade has no data → shows "Not answered" for everything.
        $submissionAnswers = DB::table('submission_answers')
            ->where('submission_id', $submission->id)
            ->pluck('answer_text', 'question_id');
        //  $submissionAnswers is now a Collection like:
        //  { "5": "A", "6": "TRUE", "7": "Some essay text..." }

        $prev = Submission::where('id', '<', $submission->id)->orderBy('id', 'desc')->first();
        $next = Submission::where('id', '>', $submission->id)->orderBy('id', 'asc')->first();

        return view('teacher.grading.evaluate',
            compact('submission', 'prev', 'next', 'submissionAnswers'));
    }

    /**
     * Save the evaluated grade scores back to database storage tables.
     */
    public function store(Request $request, $submission_id)
    {
        $submission = Submission::findOrFail($submission_id);

        $validated = $request->validate([
            'accuracy' => 'required|integer|min:0|max:10',
            'depth'    => 'required|integer|min:0|max:10',
            'clarity'  => 'required|integer|min:0|max:5',
            'feedback' => 'nullable|string',
        ]);

        // Read the hidden auto_score calculated inside the view layout context
        $autoScore = (int)$request->input('auto_score', 0);
        
        $manualScore = (int)$validated['accuracy'] + (int)$validated['depth'] + (int)$validated['clarity'];
        $finalScore = $autoScore + $manualScore;
        $percentage = round(($finalScore / 40) * 100, 2);

        // Synchronize rubric metrics and save notes directly to feedback column
        $submission->update([
            'accuracy_score'   => $validated['accuracy'],
            'depth_score'      => $validated['depth'],
            'clarity_score'    => $validated['clarity'],
            'total_score'      => $finalScore,
            'percentage'       => $percentage,
            'status'           => 'graded',
            'teacher_feedback' => $validated['feedback'] ?? null, // Fixed mapping to look up model parameter label configuration exactly
            'graded_by'        => Auth::id(),
            'graded_at'        => now()
        ]);

        // Route fallback action check triggers redirect parameters with accurate explicit signature names
        if ($request->input('action') === 'save_next') {
            $next = Submission::where('id', '>', $submission->id)->orderBy('id', 'asc')->first();
            if ($next) {
                return redirect()->route('teacher.grading.show', ['id' => $next->id]);
            }
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