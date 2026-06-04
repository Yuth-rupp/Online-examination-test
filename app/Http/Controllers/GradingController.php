<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use Carbon\Carbon;

class GradingController extends Controller
{
    /**
     * Display the grading view layout panel with navigation.
     * Maps to: /teacher/grading/{student_id}
     */
    public function show($student_id)
    {
        // Fetch submission matching the dynamic route parameter along with structural relationships
        $submission = Submission::with(['student', 'exam'])->findOrFail($student_id);
        
        // Fetch previous student submission row within the same exam assignment
        $prev = Submission::where('exam_id', $submission->exam_id)
                          ->where('id', '<', $student_id)
                          ->latest()
                          ->first();

        // Fetch next student submission row within the same exam assignment
        $next = Submission::where('exam_id', $submission->exam_id)
                          ->where('id', '>', $student_id)
                          ->first();

        // 🌟 FIXED: Correct dot notation mapping to match your teacher.grading.blade.php file location
        return view('teacher.grading', compact('submission', 'prev', 'next'));
    }

    /**
     * Store the rubric slider scores and manage transition redirection states.
     * Maps to: /teacher/grading/{student_id}/save
     */
    public function store(Request $request, $student_id)
    {
        $validated = $request->validate([
            'accuracy' => 'required|integer|between:0,10',
            'depth'    => 'required|integer|between:0,10',
            'clarity'  => 'required|integer|between:0,5',
            'feedback' => 'nullable|string',
        ]);

        $submission = Submission::findOrFail($student_id);

        // Sum values matching your maximum 25-point limit layout metric rule
        $totalScore = $validated['accuracy'] + $validated['depth'] + $validated['clarity'];
        
        // Map pass threshold state flag (Pass mark configured at 15 points)
        $isPassed = $totalScore >= 15;

        // Persist values straight into your phpMyAdmin table columns
        $submission->update([
            'accuracy_score'    => $validated['accuracy'],
            'depth_score'       => $validated['depth'],
            'clarity_score'     => $validated['clarity'],
            'total_score'       => $totalScore,
            'is_passed'         => $isPassed,
            'teacher_feedback'  => $validated['feedback'],
            'status'            => 'graded',
            'graded_by'         => auth()->id(), // Tracks authenticated teacher user_id
            'graded_at'         => Carbon::now()
        ]);

        // If user clicked "Save & Next", find the next row entry dynamically
        if ($request->input('action') === 'save_next') {
            $next = Submission::where('exam_id', $submission->exam_id)
                              ->where('id', '>', $student_id)
                              ->first();

            if ($next) {
                return redirect()->route('teacher.grading.show', $next->id)
                                 ->with('success', 'Grades updated successfully! Moved to next student.');
            }
        }

        return redirect()->back()->with('success', 'Student grading metrics updated successfully!');
    }
}