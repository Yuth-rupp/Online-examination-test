<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class GradingController extends Controller
{
    /**
     * Display the main interactive workspace grading layout dashboard sheet form.
     *
     * @param  int  $submission_id
     * @return \Illuminate\View\View
     */
    public function show($submission_id)
    {
        // 1. Fetch the exact single submission matching the requested profile path target
        // Updated to explicitly look up via submission entry ID to protect route stability
        $submission = Submission::with('student')->findOrFail($submission_id);

        // 2. Locate the sequential sorting boundaries for Previous and Next pagination hooks
        $prev = Submission::where('exam_id', $submission->exam_id)
            ->where('id', '<', $submission->id)
            ->orderBy('id', 'desc')
            ->first();

        $next = Submission::where('exam_id', $submission->exam_id)
            ->where('id', '>', $submission->id)
            ->orderBy('id', 'asc')
            ->first();

        // 3. Render the interactive workspace panel form layer passing data states
        return view('teacher.grading', compact('submission', 'prev', 'next'));
    }

    /**
     * Process grading parameters form post array validation and handle persistence data operations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $submission_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $submission_id)
    {
        // 1. Validate form fields parameters coming from the user range sliders and feedback field
        $validated = $request->validate([
            'accuracy' => 'required|integer|min:0|max:10',
            'depth'    => 'required|integer|min:0|max:10',
            'clarity'  => 'required|integer|min:0|max:5',
            'feedback' => 'nullable|string|max:1000',
            'action'   => 'required|string|in:save,save_next'
        ]);

        // 2. Find matching model record data entity securely
        $submission = Submission::findOrFail($submission_id);

        // 3. Process score summation counters mappings 
        $totalScore = $validated['accuracy'] + $validated['depth'] + $validated['clarity'];

        // 4. Update the database record attributes cleanly
        $submission->update([
            'accuracy_score' => $validated['accuracy'],
            'depth_score'    => $validated['depth'],
            'clarity_score'  => $validated['clarity'],
            'total_score'    => $totalScore,
            'feedback'       => $validated['feedback'],
            'status'         => 'graded', // Updates state to flag assignment completion progress counters
        ]);

        // 5. If "Save & Next" was selected, send them to the intermediate confirmation/preview view layout
        if ($validated['action'] === 'save_next') {
            return redirect()->route('teacher.grading.success', $submission->id);
        }

        // Otherwise, reload current worksheet form with standard success popups alerts state
        return redirect()->back()->with('success', 'Grade Saved Successfully!');
    }

    /**
     * Stream confirmation preview summary milestone layouts showing grading speed metrics tracking counters.
     *
     * @param  int  $submission_id
     * @return \Illuminate\View\View
     */
    public function success($submission_id)
    {
        // 1. Fetch the exact single entity that was just evaluated with its student profile mapping
        $lastGraded = Submission::with('student')->findOrFail($submission_id);

        // 2. Fetch the immediate next pending student submission matching the exact same exam tree layout queue
        $nextStudent = Submission::with('student')
            ->where('exam_id', $lastGraded->exam_id)
            ->where('id', '>', $lastGraded->id)
            ->orderBy('id', 'asc')
            ->first();

        // 3. Aggregate tracking statistics variables dynamically matching current status parameters
        $totalStudents = Submission::where('exam_id', $lastGraded->exam_id)->count();
        
        $completedCount = Submission::where('exam_id', $lastGraded->exam_id)
            ->where('status', 'graded')
            ->count();

        $remainingCount = $totalStudents - $completedCount;
        
        $progressPercentage = $totalStudents > 0 
            ? round(($completedCount / $totalStudents) * 100) 
            : 0;

        // 4. Send metrics payloads down to your beautifully styled confirmation view file layout
        return view('teacher.grading_confirmation', compact(
            'lastGraded', 
            'nextStudent', 
            'totalStudents', 
            'completedCount', 
            'remainingCount', 
            'progressPercentage'
        ));
    }
}