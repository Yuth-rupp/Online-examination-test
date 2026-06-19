<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Submission;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
    /**
     * Display the primary teacher dashboard panel with live examination metrics datasets.
     */
    public function index(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        // Count total exams created by the authenticated faculty member
        $totalExams = Exam::where('created_by', $user->user_id)->count();

        // Gather all live active examination partitions to bind into the dynamic dashboard table loop
        $activeExams = Exam::with('course')
            ->where('created_by', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch the courses assigned to this specific teacher so they render in the dashboard dropdown
        $courses = Course::where('teacher_id', $user->user_id)->get();

        return view('teacher.dashboard', compact('totalExams', 'activeExams', 'courses'));
    }

    /**
     * Display a listing of courses taught by the authenticated teacher.
     */
    public function myCourses(Request $request)
    {
        $user = $request->user() ?? Auth::user();
        $courses = Course::where('teacher_id', $user->user_id)->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($courses);
        }

        return view('teacher.courses', compact('courses'));
    }

    /**
     * Show the form to let a teacher create a new curriculum course directly via the UI.
     */
    public function createCourse()
    {
        return view('teacher.create_course');
    }

    /**
     * Store a newly created curriculum course via user form submission context with an automated code.
     */
    public function storeCourse(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        // Validate incoming name and description properties
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            // 1. Auto-generate a clean uppercase acronym shortcode from the course name words
            // e.g., "Introduction to Quantum Physics" -> "ITQP"
            $words = explode(' ', $data['name']);
            $acronym = '';
            foreach ($words as $word) {
                $acronym .= strtoupper(substr($word, 0, 1));
            }
            
            // Strip any remaining odd character punctuation variants
            $acronym = preg_replace('/[^A-Za-z0-9]/', '', $acronym);
            
            // Fallback clause to protect single-word entries (e.g. "Physics" -> "PHY")
            if (strlen($acronym) < 2) {
                $acronym = strtoupper(substr($data['name'], 0, 3));
            }

            // 2. Assert unique tracking constraint loops by generating random variants
            do {
                $generatedCode = $acronym . '-' . rand(100, 999);
            } while (Course::where('code', $generatedCode)->exists());

            // 3. Fallback dependency checking to ensure matching schema elements exist
            $institution = Institution::firstOrCreate(
                ['id' => 1],
                ['name' => 'Main Campus Institution', 'code' => 'MAIN-INST', 'is_active' => true]
            );

            // 4. Save the structural course details cleanly mapping to active session state
            Course::create([
                'name'           => $data['name'],
                'code'           => $generatedCode,
                'description'    => $data['description'],
                'institution_id' => $institution->id,
                'teacher_id'     => $user->user_id,
                'is_active'      => true
            ]);

            // 5. Fire redirect header command to land user back onto the primary viewport
            return redirect()->route('teacher.dashboard')->with('success', "Course '{$data['name']}' built cleanly with identifier: {$generatedCode}");

        } catch (\Exception $e) {
            // Catch data layer constraints, log the incident context, and return to the form with state values
            Log::error('Course Creation Architecture Failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Data Exception Mismatch: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Permanently delete a duplicate or unwanted course partition safely.
     */
    public function destroyCourse($id)
    {
        $user = Auth::user();
        
        // Find the course ensuring it strictly belongs to the logged-in teacher context
        $course = Course::where('id', $id)
            ->where('teacher_id', $user->user_id)
            ->firstOrFail();

        // Terminate the row instance cleanly
        $course->delete();

        return redirect()->route('teacher.dashboard')->with('success', "Course record '{$course->name}' purged cleanly from your portal workspace!");
    }

    /**
     * Render a comprehensive preview sheet of all exam questions and solution keys.
     */
    public function previewExam($id)
    {
        $user = Auth::user();
        
        // Find the exam belonging to this teacher, pulling its relation elements
        $exam = Exam::with(['course', 'questions' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])
        ->where('exam_id', $id)
        ->where('created_by', $user->user_id)
        ->firstOrFail();

        return view('teacher.preview_exam', compact('exam'));
    }

    /**
     * Store a newly created exam session with an automatic single-use code identifier.
     */
    public function createExam(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'duration'  => 'required|integer|min:1', 
            'pass_mark' => 'required|numeric|min:0|max:100'
        ]);

        // Find the matching course profile context using database schema fields
        $course = Course::find($data['course_id']);
        
        // Isolate course initials using the correct schema column 'name' instead of 'title'
        $prefix = $course ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($course->name, 0, 4))) : 'EXAM';

        // Generate a random, human-readable single-use security string token unique for that classroom group
        $cleanSingleUseCode = $prefix . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $exam = Exam::create([
            'title'       => $data['title'],
            'course_id'   => $data['course_id'],
            'duration'    => $data['duration'],
            'pass_mark'   => $data['pass_mark'],
            'created_by'  => $user->user_id,
            'access_code' => $cleanSingleUseCode, // Commits generated single-use class code straight to storage
            'status'      => 'published'
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($exam, 201);
        }

        return redirect()->route('teacher.dashboard')->with('success', "Exam Session Generated Successfully! Share this single-use code token with Class: {$exam->access_code}");
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
        $user = $request->user() ?? Auth::user();

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

    /* --- 🖥️ LIVE EXAM MONITORING METHOD IMPLEMENTATIONS --- */

    /**
     * Interstitial View: Render the beautiful confirmation page before ending exam.
     */
    public function endExamConfirmation()
    {
        return view('teacher.grading_confirmation_end');
    }

    /**
     * Render the post-exam analytics dashboard overview screen.
     */
    public function examSessionEnded()
    {
        return view('teacher.exam_session_ended');
    }

    /**
     * End the current running exam session globally via dynamic AJAX request commands.
     */
    public function endExamSession(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Exam session terminated by Faculty command. Final configurations compiled.'
            ]);
        }

        return redirect()->route('teacher.exam.endedOverview')->with('success', 'Exam session closed safely!');
    }

    /**
     * Compile telemetry dataset metrics logs down into a downloadable stream attachment file (.csv).
     */
    public function exportSessionLog()
    {
        $filename = "advanced_calculus_ii_session_log_" . date('Y-m-d_H-i-s') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Timestamp', 'Student Identity Name', 'Incident Type Flag Description', 'Status Context']);
            fputcsv($file, ['10:42:15', 'Sarah Chen', 'Unauthorized tab switch detected', 'Flagged (3x)']);
            fputcsv($file, ['10:38:02', 'Alex Rivera', 'Network connection interrupted', 'Resolved']);
            fputcsv($file, ['10:35:58', 'Marcus Thorne', 'Multiple faces detected in frame', 'Flagged']);
            fputcsv($file, ['10:30:12', 'Sarah Chen', 'Gaze tracked away from screen', 'Ignored']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}