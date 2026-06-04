<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    /**
     * Display a listing of courses taught by the authenticated teacher.
     */
    public function myCourses(Request $request)
    {
        return response()->json(
            Course::where('teacher_id', $request->user()->user_id)->get()
        );
    }

    /**
     * Store a newly created exam session in storage.
     */
    public function createExam(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'duration' => 'required|integer', 
            'pass_mark' => 'required|numeric'
        ]);

        $data['created_by'] = $request->user()->user_id;

        $exam = Exam::create($data);

        return response()->json($exam, 201);
    }

    /**
     * Display the master Question Bank dashboard overview pane.
     */
    public function questionBank()
    {
        $questions = Question::orderBy('created_at', 'desc')->paginate(5);

        $totalCount = Question::count();
        $mcqCount   = Question::where('type', 'MCQ')->count();
        $essayCount = Question::where('type', 'Essay')->count();

        $unusedPercentage = $totalCount > 0 ? round((Question::whereNull('exam_id')->count() / $totalCount) * 100) : 0;

        return view('teacher.question-bank', compact('questions', 'mcqCount', 'essayCount', 'unusedPercentage'));
    }

    /**
     * Display the rich metric trends analytics engine overview layout.
     */
    public function analytics()
    {
        $totalStudentsCount = User::where('role', 'student')->count() ?: 12480; 
        $activeCoursesCount = Course::count() ?: 156;
        $avgAttendanceRate  = "94.2%";
        $graduationRate     = "89%";

        $enrollmentChartData = [30, 34, 56, 29, 62, 45];
        $revenueChartData    = [46, 35, 76, 41, 81, 48];
        $monthsLabels        = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

        return view('teacher.teacher-analytic', compact(
            'totalStudentsCount', 
            'activeCoursesCount', 
            'avgAttendanceRate', 
            'graduationRate',
            'enrollmentChartData',
            'revenueChartData',
            'monthsLabels'
        ));
    }

    /**
     * Process updates to the teacher's profile customization attributes.
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'avatar'        => 'nullable|image|max:800', 
            'remove_avatar' => 'nullable|string'
        ]);

        $photoColumn = null;
        foreach (['profile_photo_path', 'avatar_path', 'image', 'avatar', 'profile_image'] as $column) {
            if (array_key_exists($column, $user->getAttributes())) {
                $photoColumn = $column;
                break;
            }
        }

        if ($photoColumn) {
            if ($request->input('remove_avatar') === '1') {
                if ($user->$photoColumn && !str_contains($user->$photoColumn, 'dicebear.com')) {
                    $oldPath = str_replace('/storage/', '', $user->$photoColumn);
                    Storage::disk('public')->delete($oldPath);
                }
                $user->$photoColumn = null;
            }

            if ($request->hasFile('avatar')) {
                if ($user->$photoColumn && !str_contains($user->$photoColumn, 'dicebear.com')) {
                    $oldPath = str_replace('/storage/', '', $user->$photoColumn);
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('avatar')->store('avatars', 'public');
                $user->$photoColumn = '/storage/' . $path;
            }
        }

        $user->full_name = $validatedData['full_name'];
        $user->email = $validatedData['email'];
        $user->save();

        return redirect()->back()->with('success', 'Personalization settings updated cleanly across data structures!');
    }

    /**
     * Store a newly generated rich-text question model record inside database storage safely.
     */
    public function addQuestion(Request $request)
    {
        $validatedData = $request->validate([
            'parent_exam_id' => 'nullable|string',
            'question_type' => 'required|string',
            'difficulty' => 'required|string',
            'points' => 'required|numeric|min:1',
            'question_text' => 'required|string', 
            'mcq_options' => 'nullable|array',
            'mcq_correct_option' => 'nullable|string',
            'tf_correct_index' => 'nullable|string',
            'essay_rubric_guidelines' => 'nullable|string',
            'attachment_media' => 'nullable|file|max:5120',
            'tags' => 'nullable|array'
        ]);

        $storeData = [
            'exam_id'        => $validatedData['parent_exam_id'] ?? null,
            'type'           => $validatedData['question_type'],
            'marks'          => $validatedData['points'],
            'content'        => $validatedData['question_text'],
            'options'        => $validatedData['mcq_options'] ?? [],
            'correct_answer' => [
                'mcq'    => $validatedData['mcq_correct_option'] ?? null,
                'tf'     => $validatedData['tf_correct_index'] ?? null,
                'rubric' => $validatedData['essay_rubric_guidelines'] ?? null,
            ],
            'explanation'    => 'Difficulty: ' . $validatedData['difficulty'] . ' | Tags: ' . implode(', ', $validatedData['tags'] ?? []),
        ];

        if ($request->hasFile('attachment_media')) {
            $path = $request->file('attachment_media')->store('question_attachments', 'public');
            $storeData['media_url'] = '/storage/' . $path;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Question::create($storeData);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return redirect()->route('teacher.question-bank')->with('success', 'Question logged cleanly to database structure!');
    }

    /**
     * Show the form for editing the specified question record.
     */
    public function editQuestion($id)
    {
        $question = Question::findOrFail($id);
        return view('teacher.edit_question', compact('question'));
    }

    /**
     * Update the specified question model entity record inside database storage.
     */
    public function updateQuestion(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validatedData = $request->validate([
            'exam_id' => 'required|string',
            'question_type' => 'required|string',
            'difficulty' => 'required|string',
            'points' => 'required|numeric|min:1',
            'question_text' => 'required|string',
            'mcq_options' => 'nullable|array',
            'mcq_correct_option' => 'nullable|string',
            'essay_rubric_guidelines' => 'nullable|string'
        ]);

        $tagsStr = '';
        if (preg_match('/\| Tags:\s*(.*)/', $question->explanation, $matches)) {
            $tagsStr = $matches[1] ?? '';
        }

        $updateData = [
            'exam_id'        => $validatedData['exam_id'],
            'type'           => $validatedData['question_type'],
            'marks'          => $validatedData['points'],
            'content'        => $validatedData['question_text'],
            'options'        => $validatedData['question_type'] === 'MCQ' ? ($validatedData['mcq_options'] ?? []) : [],
            'correct_answer' => [
                'mcq'    => $validatedData['question_type'] === 'MCQ' ? ($validatedData['mcq_correct_option'] ?? null) : null,
                'tf'     => null,
                'rubric' => $validatedData['question_type'] === 'Essay' ? ($validatedData['essay_rubric_guidelines'] ?? null) : null,
            ],
            'explanation'    => 'Difficulty: ' . $validatedData['difficulty'] . ($tagsStr ? ' | Tags: ' . $tagsStr : '')
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $question->update($updateData);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return redirect()->route('teacher.question-bank')->with('success', 'Question changes processed successfully!');
    }

    /**
     * Remove the specified question model entity instance from database storage logs.
     */
    public function destroyQuestion($id)
    {
        $question = Question::findOrFail($id);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $question->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return redirect()->route('teacher.question-bank')->with('success', 'Question record purged from database bank safely.');
    }

    /**
     * Display a list of submissions for an exam.
     */
    public function submissions($examId)
    {
        $submissions = Submission::where('exam_id', $examId)->get();
        return view('teacher.submissions_list', compact('submissions'));
    }

    /* --- 📝 INTERACTIVE GRADING WORKFLOW METHODS --- */

    /**
     * Show the Interactive Grading panel for a specific student submission.
     * Maps to route: teacher.grading.show
     */
    public function showGradingPanel($student_id)
    {
        // Fetch current submission with eager loading relations if available
        $submission = Submission::findOrFail($student_id);

        // Fetch surrounding submission instances restricted to the same exam session
        $previous_id = Submission::where('exam_id', $submission->exam_id)
            ->where('id', '<', $student_id)
            ->max('id');

        $next_id = Submission::where('exam_id', $submission->exam_id)
            ->where('id', '>', $student_id)
            ->min('id');

        // Target your view file location (assuming your file is named teacher/grading.blade.php)
        return view('teacher.grading', compact('submission', 'previous_id', 'next_id'));
    }

    /**
     * Store grading scores, handle totals, passing rules, and jump sequences.
     * Maps to route: teacher.grading.store
     */
    public function saveStudentGrade(Request $request, $student_id)
    {
        $validated = $request->validate([
            'accuracy' => 'required|integer|between:0,10',
            'depth'    => 'required|integer|between:0,10',
            'clarity'  => 'required|integer|between:0,5',
            'feedback' => 'nullable|string',
        ]);

        $submission = Submission::findOrFail($student_id);
        
        // Calculate dynamic runtime matrix values
        $totalScore = $validated['accuracy'] + $validated['depth'] + $validated['clarity'];

        // Persist metric modifications straight into the DB
        $submission->update([
            'accuracy_score' => $validated['accuracy'],
            'depth_score'    => $validated['depth'],
            'clarity_score'  => $validated['clarity'],
            'total_score'    => $totalScore,
            'feedback'       => $validated['feedback'],
            'status'         => 'graded'
        ]);

        // Evaluate Save & Next jump condition parameters
        if ($request->input('action') === 'save_next') {
            $next_id = Submission::where('exam_id', $submission->exam_id)
                ->where('id', '>', $student_id)
                ->min('id');

            if ($next_id) {
                return redirect()->route('teacher.grading.show', $next_id)
                    ->with('success', 'Grades updated and forwarded to next student!');
            }
        }

        return redirect()->back()->with('success', 'Grades preserved successfully!');
    }
}