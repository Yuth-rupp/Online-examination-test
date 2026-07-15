<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSubmission; // Assumed model for tracking user attempts
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Fetch all exams formatted with appropriate real-time evaluation states.
     * Route::get('/api/exams')
     */
    public function index()
    {
        // 1. Fetch all structural exams from the database
        $rawExams = Exam::all();
        
        // Assuming user authentication is configured to match individual score histories
        $userId = Auth::id() ?? 1; 

        // 2. Transform the collection with Carbon date parsing and evaluation logic
        $exams = $rawExams->map(function ($exam) use ($userId) {
            $startTime = Carbon::parse($exam->date);
            $endTime = $startTime->copy()->addMinutes($exam->duration_minutes);
            
            // Query if an existing attempt or grade file exists for this specific student context
            $submission = ExamSubmission::where('exam_id', $exam->id)
                ->where('user_id', $userId)
                ->first();

            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'module' => $exam->module_code, // e.g., 'Module 402'
                'department' => $exam->department_name, // e.g., 'Engineering Department'
                'date' => $startTime->toIso8601String(), // Safe format for frontend JS/Blade interaction
                'duration_minutes' => $exam->duration_minutes,
                'score' => $submission ? "{$submission->score}/100" : null,
            ];
        });

        return response()->json($exams, 200);
    }

    /**
     * Handle concrete exam payload validation, grading evaluation, and response tracking.
     * Route::post('/api/exams/{id}/submit')
     */
    public function submit(Request $request, $id)
    {
        // 1. Validate the incoming student answer payload configuration
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.selected_option' => 'required|string',
        ]);

        // 2. Look up the specific exam matrix asset
        $exam = Exam::with('questions')->find($id);

        if (!$exam) {
            return response()->json(['message' => 'Exam configuration profile not found.'], 404);
        }

        // 3. Confirm real-time slot validity (Prevent late server-side submittals)
        $startTime = Carbon::parse($exam->date);
        $endTime = $startTime->copy()->addMinutes($exam->duration_minutes + 5); // 5-minute buffer allowance
        
        if (Carbon::now()->gt($endTime)) {
            return response()->json(['message' => 'Submission window for this assessment has closed.'], 403);
        }

        // 4. Scoring logic iteration
        $totalQuestions = $exam->questions->count();
        $correctAnswers = 0;

        foreach ($validated['answers'] as $submittedAnswer) {
            $question = $exam->questions->firstWhere('id', $submittedAnswer['question_id']);
            
            if ($question && $question->correct_option === $submittedAnswer['selected_option']) {
                $correctAnswers++;
            }
        }

        // Calculate normalized scale integer score out of 100
        $finalScore = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;

        // 5. Persist structural outcome records to the database
        $submission = ExamSubmission::updateOrCreate(
            [
                'exam_id' => $exam->id,
                'user_id' => Auth::id() ?? 1, // Fallback placeholder ID for development setups
            ],
            [
                'score' => $finalScore,
                'submitted_at' => Carbon::now(),
                'answers_payload' => json_encode($validated['answers']),
            ]
        );

        return response()->json([
            'message' => 'Exam assessment evaluating pipeline completed successfully.',
            'summary' => [
                'score' => "{$finalScore}/100",
                'correct_count' => $correctAnswers,
                'total_questions' => $totalQuestions,
            ]
        ], 200);
    }
}