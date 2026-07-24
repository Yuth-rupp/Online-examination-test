<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Submission;
use App\Models\User;
use App\Models\Institution;
use App\Models\Question;
use App\Models\Enrollment;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TeacherController extends Controller
{
    /**
     * Display the primary teacher dashboard panel with live examination metrics datasets.
     */
    public function index(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        // Base collections scoped strictly to THIS teacher's own data.
        $teacherExams   = Exam::where('created_by', $user->user_id)->get();
        $teacherExamIds = $teacherExams->pluck('exam_id');

        $totalExams = $teacherExams->count();

        $activeExams = Exam::with('course')
            ->where('created_by', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $courses = Course::where('teacher_id', $user->user_id)->get();

        // "+X this week" — real count of exams this teacher created in the last 7 days.
        $examsThisWeek = $teacherExams->where('created_at', '>=', now()->subDays(7))->count();

        // Currently live/published exam sessions belonging to this teacher.
        $activeSessionsCount = $teacherExams->where('status', 'published')->count();

        // Distinct students enrolled in THIS teacher's courses (not the whole platform).
        $courseIds = $courses->pluck('id');
        $enrolledStudentsCount = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        // Grading stats scoped to submissions for exams THIS teacher created.
        $totalSubmissions = Submission::whereIn('exam_id', $teacherExamIds)->count();

        $pendingGradingCount = Submission::whereIn('exam_id', $teacherExamIds)
            ->where('status', 'pending_grading')
            ->count();

        $gradedCount = Submission::whereIn('exam_id', $teacherExamIds)
            ->where('status', 'graded')
            ->count();

        $gradingCompletionPercent = $totalSubmissions > 0
            ? round(($gradedCount / $totalSubmissions) * 100)
            : 0;

        // Pass rate calculated only from this teacher's graded submissions.
        $passRate = $gradedCount > 0
            ? round(
                Submission::whereIn('exam_id', $teacherExamIds)
                    ->where('status', 'graded')
                    ->where('is_passed', true)
                    ->count() / $gradedCount * 100
              )
            : 0;

        // Real unread notification count for the bell/"Alerts" badges.
        // For a freshly registered account this is simply 0 — no fake numbers.
        $unreadNotificationCount = Notification::where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->count();

        return view('teacher.dashboard', compact(
            'totalExams',
            'activeExams',
            'courses',
            'examsThisWeek',
            'activeSessionsCount',
            'enrolledStudentsCount',
            'totalSubmissions',
            'pendingGradingCount',
            'gradedCount',
            'gradingCompletionPercent',
            'passRate',
            'unreadNotificationCount'
        ));
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

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $words = explode(' ', $data['name']);
            $acronym = '';
            foreach ($words as $word) {
                $acronym .= strtoupper(substr($word, 0, 1));
            }
            
            $acronym = preg_replace('/[^A-Za-z0-9]/', '', $acronym);
            
            if (strlen($acronym) < 2) {
                $acronym = strtoupper(substr($data['name'], 0, 3));
            }

            do {
                $generatedCode = $acronym . '-' . rand(100, 999);
            } while (Course::where('code', $generatedCode)->exists());

            $institution = Institution::firstOrCreate(
                ['id' => 1],
                ['name' => 'Main Campus Institution', 'code' => 'MAIN-INST', 'is_active' => true]
            );

            Course::create([
                'name'           => $data['name'],
                'code'           => $generatedCode,
                'description'    => $data['description'],
                'institution_id' => $institution->id,
                'teacher_id'     => $user->user_id,
                'is_active'      => true
            ]);

            return redirect()->route('teacher.dashboard')->with('success', "Course built cleanly.");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Permanently delete a duplicate or unwanted course partition safely.
     */
    public function destroyCourse($id)
    {
        $user = Auth::user();
        
        $course = Course::where('id', $id)
            ->where('teacher_id', $user->user_id)
            ->firstOrFail();

        $course->delete();

        return redirect()->route('teacher.dashboard')->with('success', "Course record purged cleanly.");
    }

    /**
     * Render a comprehensive preview sheet of all exam questions and solution keys.
     */
    public function previewExam($id)
    {
        $user = Auth::user();
        
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

        $course = Course::find($data['course_id']);
        $prefix = $course ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($course->name, 0, 4))) : 'EXAM';
        $cleanSingleUseCode = $prefix . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        $exam = Exam::create([
            'title'       => $data['title'],
            'course_id'   => $data['course_id'],
            'duration'    => $data['duration'],
            'pass_mark'   => $data['pass_mark'],
            'created_by'  => $user->user_id,
            'access_code' => $cleanSingleUseCode,
            'status'      => 'published'
        ]);

        $this->notifyEnrolledStudents($exam, $course);

        return redirect()->route('teacher.dashboard')->with('success', "Exam Session Generated: {$exam->access_code}");
    }

    /**
     * Push a "new exam published" notification to every student actively
     * enrolled in the exam's course. Each Notification::create() below
     * is picked up by NotificationObserver and broadcast live to that
     * student's bell on Dashboard, History, Exams, and Settings.
     */
    private function notifyEnrolledStudents(Exam $exam, ?Course $course): void
    {
        if (!$course) {
            return;
        }

        $studentIds = Enrollment::where('course_id', $course->id)
            ->where('status', 'active')
            ->pluck('user_id');

        foreach ($studentIds as $studentId) {
            Notification::create([
                'user_id' => $studentId,
                'title'   => 'New Exam Published',
                'body'    => "\"{$exam->title}\" is now available for {$course->name} (Course ID: {$course->id}).",
                'type'    => 'info',
                'data'    => [
                    'exam_id'   => $exam->exam_id,
                    'course_id' => $course->id,
                ],
            ]);
        }
    }

    /**
     * Store a newly created exam session generated via the real-time API (Question Bank Modal).
     */
    public function storeApiExam(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title'        => 'required|string|max:255',
            'duration'     => 'required|integer|min:1',
            'pass_mark'    => 'required|numeric|min:0|max:100',
            'question_ids' => 'required|array'
        ]);

        try {
            $prefix = 'EXAM';
            $cleanSingleUseCode = $prefix . '-' . str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $defaultCourse = Course::where('teacher_id', $user->user_id)->first();
            $courseId = $defaultCourse ? $defaultCourse->id : 1; 

            $exam = Exam::create([
                'title'       => $request->title,
                'course_id'   => $courseId,
                'duration'    => $request->duration,
                'pass_mark'   => $request->pass_mark,
                'created_by'  => $user->user_id,
                'access_code' => $cleanSingleUseCode,
                'status'      => 'published'
            ]);

            Question::whereIn('id', $request->question_ids)->update(['exam_id' => $exam->exam_id]);

            $this->notifyEnrolledStudents($exam, $defaultCourse);

            return response()->json([
                'success' => true,
                'token'   => $cleanSingleUseCode
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the master Question Bank dashboard overview pane with optional parameters filtering.
     */
    public function questionBank(Request $request)
    {
        $query = Question::with('questionBank')->orderBy('created_at', 'desc');

        if ($request->has('course_id') && !empty($request->course_id)) {
            $query->where('exam_id', $request->course_id);
        }

        $questions = $query->paginate(5)->appends($request->query());

        $totalCount = Question::count();
        $mcqCount   = Question::where('type', 'MCQ')->count();
        $essayCount = Question::where('type', 'Essay')->count();

        $unusedPercentage = $totalCount > 0 ? round((Question::whereNull('exam_id')->count() / $totalCount) * 100) : 0;

        return view('teacher.question-bank', compact('questions', 'mcqCount', 'essayCount', 'unusedPercentage'));
    }

    /**
     * Display the rich real-time exam metrics trends analytics engine workspace.
     */
    public function analytics()
    {
        $data = $this->buildAnalyticsPayload(Auth::user());

        return view('teacher.teacher-analytic', $data);
    }

    /**
     * JSON endpoint used by the Analytics page to poll for fresh numbers
     * every few seconds — and by Echo's instant-refresh listener the
     * moment a submission is graded — so the dashboard that's already
     * labelled "LIVE" actually behaves that way instead of only ever
     * reflecting whatever was true at the moment the page first loaded.
     */
    public function analyticsLiveData(Request $request)
    {
        $data = $this->buildAnalyticsPayload($request->user() ?? Auth::user());

        return response()->json([
            'totalStudentsCount'     => $data['totalStudentsCount'],
            'activeSessionsCount'    => $data['activeSessionsCount'],
            'averageClassScore'      => $data['averageClassScore'],
            'examPassRatePercentage' => $data['examPassRatePercentage'],
            'totalSubmissionsCount'  => $data['totalSubmissionsCount'],
            'liveSubmissionsRaw'     => $data['liveSubmissionsRaw'],
            'generated_at'           => now()->toIso8601String(),
        ]);
    }

    /**
     * Shared data builder behind both the Analytics page and its live
     * polling endpoint, so the two can never drift out of sync with
     * each other.
     */
    private function buildAnalyticsPayload($user): array
    {
        // 1. Gather master collections owned by this authenticated teacher
        $teacherExams = Exam::where('created_by', $user->user_id)->get();
        $teacherExamIds = $teacherExams->pluck('exam_id');
        $activeSessionsCount = $teacherExams->where('status', 'published')->count();

        $totalStudentsCount = User::where('role', 'student')->count();
        $totalSubmissionsCount = Submission::whereIn('exam_id', $teacherExamIds)->count();

        // FIXED: Structural schema lookups when table records are empty
        $scoreColumn = 'score';
        $columns = Schema::getColumnListing('submissions');
        
        if (in_array('marks', $columns)) {
            $scoreColumn = 'marks';
        } elseif (in_array('total_score', $columns)) {
            $scoreColumn = 'total_score';
        } elseif (in_array('points', $columns)) {
            $scoreColumn = 'points';
        }

        // Calculate Global Class Average Score safely
        $averageClassScore = $totalSubmissionsCount > 0 
            ? round(Submission::whereIn('exam_id', $teacherExamIds)->avg($scoreColumn), 1) 
            : 0;

        // Calculate live Global Passing Rate Ratio Percentage
        // NOTE: pass_mark on `exams` is stored as a 0-100 percentage threshold,
        // so it must be compared against the submission's `percentage` column
        // (or the stored is_passed flag), never against a raw marks column
        // such as total_score/marks/points, whose scale depends on the exam's
        // total possible points and isn't necessarily out of 100.
        if ($totalSubmissionsCount > 0) {
            $passedCount = Submission::whereIn('exam_id', $teacherExamIds)
                ->where('is_passed', true)
                ->count();
            $examPassRatePercentage = round(($passedCount / $totalSubmissionsCount) * 100);
        } else {
            $examPassRatePercentage = 0;
        }

        // 2. Fetch all raw submissions for client-side filtering engine processing
        $liveSubmissionsRaw = Submission::whereIn('submissions.exam_id', $teacherExamIds)
            ->join('users', 'submissions.user_id', '=', 'users.user_id')
            ->join('exams', 'submissions.exam_id', '=', 'exams.exam_id')
            ->join('courses', 'exams.course_id', '=', 'courses.id')
            ->select([
                'submissions.id',
                'users.user_id as student_id',
                'users.full_name as student_name',
                'courses.id as course_id',
                'courses.name as course_name',
                'exams.exam_id as exam_id',
                'exams.title as exam_title',
                // student_score is intentionally the normalized percentage, not the
                // raw {$scoreColumn} marks — the frontend compares it directly against
                // passing_mark, which is itself a 0-100 percentage threshold.
                'submissions.percentage as student_score',
                'exams.pass_mark as passing_mark',
                'submissions.created_at'
            ])
            ->orderBy('submissions.created_at', 'desc')
            ->get();

        // 3. Identify Hardest Question components missed by students dynamically
        //
        // ✅ FIX: fail_rate used to be rand() — a random number reseeded on
        // every page load, completely disconnected from how students
        // actually answered. Compute it for real from submission_answers,
        // comparing each stored answer against the question's correct_option
        // (same comparison GradingController/StudentController use
        // elsewhere), and only rank auto-gradable questions (MCQ/True-False)
        // since essay questions don't have a single correct_option to
        // compare against.
        $hardestQuestions = Question::whereIn('exam_id', $teacherExamIds)
            ->whereIn('type', ['MCQ', 'TRUE/FALSE'])
            ->select('id', 'content', 'type', 'difficulty', 'points', 'correct_option')
            ->get()
            ->map(function ($q) {
                $answers = DB::table('submission_answers')
                    ->where('question_id', $q->id)
                    ->pluck('answer_text');

                $attempts = $answers->count();
                $correctAnswer = strtolower(trim((string) $q->correct_option));
                $correctCount = $answers->filter(
                    fn ($a) => strtolower(trim((string) $a)) === $correctAnswer
                )->count();

                $q->attempts  = $attempts;
                $q->fail_rate = $attempts > 0
                    ? round((($attempts - $correctCount) / $attempts) * 100)
                    : 0;

                return $q;
            })
            // Only rank questions that have actually been attempted —
            // an unanswered question isn't "hard", it's just unused.
            ->filter(fn ($q) => $q->attempts > 0)
            ->sortByDesc('fail_rate')
            ->take(3)
            ->values();

        // 4. Real system notifications for this teacher (empty for a fresh account —
        //    no more fake "New exam submission..." placeholders).
        $notifications = Notification::where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get()
            ->map(fn ($n) => [
                'id'    => $n->id,
                'text'  => $n->title . ($n->body ? ' — ' . $n->body : ''),
                'time'  => $n->created_at?->diffForHumans(),
                'read'  => (bool) $n->read_at,
            ]);

        $monthsLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        return compact(
            'totalStudentsCount',
            'activeSessionsCount',
            'averageClassScore',
            'examPassRatePercentage',
            'totalSubmissionsCount',
            'monthsLabels',
            'notifications',
            'hardestQuestions',
            'teacherExams',
            'liveSubmissionsRaw'
        );
    }

    /**
     * Instantly upload (or remove) the teacher's profile photo via AJAX.
     * Used by Settings so the avatar updates immediately without a full
     * page/form submit — and always returns an absolute URL so the image
     * never breaks regardless of which page it's rendered on.
     */
    public function uploadAvatar(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile ?? $user->profile()->create(['first_name' => $user->full_name, 'last_name' => '']);

        // ── Removal ──
        if ($request->boolean('remove')) {
            if (!empty($profile->avatar_url)) {
                $this->deleteStoredAvatar($profile->avatar_url);
            }
            $profile->avatar_url = null;
            $profile->save();

            return response()->json([
                'success'    => true,
                'avatar_url' => null,
            ]);
        }

        // ── Upload ──
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if (!empty($profile->avatar_url)) {
            $this->deleteStoredAvatar($profile->avatar_url);
        }

        // Stored through the 'public' disk (storage/app/public/profile_photos,
        // exposed via the storage:link symlink) so it works the same way as
        // the student/admin uploads and survives across environments that
        // don't share the plain public/ folder.
        $path = $request->file('avatar')->store('profile_photos', 'public');

        $profile->avatar_url = $path;
        $profile->save();

        return response()->json([
            'success'    => true,
            'avatar_url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Delete a previously stored avatar, regardless of whether it was saved
     * via the 'public' disk (new uploads) or the legacy public_path()/move()
     * approach (older uploads made before this was standardized).
     */
    private function deleteStoredAvatar(string $storedPath): void
    {
        if (Storage::disk('public')->exists($storedPath)) {
            Storage::disk('public')->delete($storedPath);
            return;
        }

        if (file_exists(public_path($storedPath))) {
            @unlink(public_path($storedPath));
        }
    }

    /**
     * Process updates to the teacher's profile personalization attributes.
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_avatar' => 'nullable|in:0,1',
        ]);

        $user->full_name = $validatedData['full_name'];
        $user->email = $validatedData['email'];
        $user->save();

        // Avatar is now handled instantly by uploadAvatar() via AJAX as soon as the
        // file is chosen on Settings, so this form submit only needs to cover the
        // case where JS didn't run (e.g. no-JS fallback) — the input will be empty
        // in the normal flow since it's cleared right after the AJAX upload succeeds.
        $profile = $user->profile ?? $user->profile()->create(['first_name' => $user->full_name, 'last_name' => '']);

        if ($request->boolean('remove_avatar')) {
            if (!empty($profile->avatar_url)) {
                $this->deleteStoredAvatar($profile->avatar_url);
            }
            $profile->avatar_url = null;
            $profile->save();
        } elseif ($request->hasFile('avatar')) {
            if (!empty($profile->avatar_url)) {
                $this->deleteStoredAvatar($profile->avatar_url);
            }

            $path = $request->file('avatar')->store('profile_photos', 'public');
            $profile->avatar_url = $path;
            $profile->save();
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Render the Question Creation Workspace.
     */
    public function createQuestion()
    {
        $exams = Exam::where('created_by', Auth::user()->user_id)
            ->orderBy('title', 'asc')
            ->get();

        return view('teacher.create_question', compact('exams'));
    }

    /**
     * Store a newly configured question element.
     */
    public function addQuestion(Request $request)
    {
        $request->validate([
            'exam_id'          => 'required|string',
            'type'             => 'required|string',
            'difficulty'       => 'required|string',
            'points'           => 'required|numeric|min:1',
            'content'          => 'required|string', 
            'attachment_media' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'questions_csv'    => 'nullable|file|mimes:csv,txt|max:4096',
        ]);

        $question = new Question();
        
        if ($request->hasFile('attachment_media')) {
            $imageFile = $request->file('attachment_media');
            $imgName = time() . '_img_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('uploads/questions'), $imgName);
            $question->media_url = 'uploads/questions/' . $imgName;
        }

        if ($request->hasFile('questions_csv')) {
            $csvFile = $request->file('questions_csv');
            $question->original_filename = $csvFile->getClientOriginalName();
            
            $csvName = time() . '_data_' . uniqid() . '.' . $csvFile->getClientOriginalExtension();
            $csvFile->move(public_path('uploads/questions'), $csvName);
            $question->csv_url = 'uploads/questions/' . $csvName;
        }

        $question->exam_id = $request->input('exam_id');
        $question->type = $request->input('type');
        $question->difficulty = $request->input('difficulty');
        $question->points = $request->input('points');
        $question->content = $request->input('content');
        $question->explanation = "Difficulty: " . $request->input('difficulty');

        if ($question->type === 'MCQ') {
            $question->option_a = $request->input('option_a');
            $question->option_b = $request->input('option_b');
            $question->option_c = $request->input('option_c');
            $question->option_d = $request->input('option_d');
            $question->correct_option = $request->input('correct_option');
        } elseif ($question->type === 'True/False') {
            $question->option_a = 'TRUE';
            $question->option_b = 'FALSE';
            $question->correct_option = strtoupper($request->input('tf_correct'));
        } else {
            $question->essay_rubric = $request->input('essay_guidelines');
        }

        $question->save();

        return redirect()->route('teacher.question-bank')
            ->with('success', 'Question logged cleanly to database structure!');
    }

    /**
     * Show the form for editing the specified question record.
     */
    public function editQuestion($id)
    {
        if (!class_exists('\App\Models\Question')) {
            abort(500, 'The Question Model entity is missing inside app/Models/.');
        }

        $question = Question::findOrFail($id);
        return view('teacher.edit_question', compact('question'));
    }

    /**
     * Update the specified question model entity record inside database storage.
     */
    public function updateQuestion(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $request->validate([
            'question_type'   => 'required|in:MCQ,TRUE/FALSE,ESSAY',
            'question_text'   => 'required|string',
            'difficulty'      => 'required|in:Easy,Medium,Hard',
            'points'          => 'required|integer|min:1',
            'question_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'question_csv'    => 'nullable|file|mimes:csv,txt|max:4096',
            'exam_id'         => 'nullable|string'
        ]);

        if ($request->input('remove_image') === '1') {
            if (!empty($question->media_url) && file_exists(public_path($question->media_url))) {
                @unlink(public_path($question->media_url));
            }
            $question->media_url = null;
        }

        if ($request->input('remove_csv') === '1') {
            if (!empty($question->csv_url) && file_exists(public_path($question->csv_url))) {
                @unlink(public_path($question->csv_url));
            }
            $question->csv_url = null;
            $question->original_filename = null;
        }

        if ($request->hasFile('question_image')) {
            if (!empty($question->media_url) && file_exists(public_path($question->media_url))) {
                @unlink(public_path($question->media_url));
            }
            $imageFile = $request->file('question_image');
            $imgName = time() . '_img_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path('uploads/questions'), $imgName);
            $question->media_url = 'uploads/questions/' . $imgName;
        }

        if ($request->hasFile('question_csv')) {
            if (!empty($question->csv_url) && file_exists(public_path($question->csv_url))) {
                @unlink(public_path($question->csv_url));
            }
            $csvFile = $request->file('question_csv');
            $question->original_filename = $csvFile->getClientOriginalName();
            
            $csvName = time() . '_data_' . uniqid() . '.' . $csvFile->getClientOriginalExtension();
            $csvFile->move(public_path('uploads/questions'), $csvName);
            $question->csv_url = 'uploads/questions/' . $csvName;
        }

        // ✅ FIX: exam_id is intentionally NOT taken from the request here.
        // It used to be `$question->exam_id = $request->input('exam_id')`,
        // driven by a plain editable text box on the edit form — a stray
        // clear/paste while editing points or content would silently
        // unlink the question from its exam. Since submission_answers has
        // no foreign key back to questions, that break was invisible until
        // a teacher opened grading and saw "No questions found" for an
        // exam students had already answered. Reassigning a question to a
        // different exam is intentionally not supported from this form —
        // the question keeps whatever exam it already belongs to.
        $question->type = $request->input('question_type');
        $question->content = $request->input('question_text');
        $question->difficulty = $request->input('difficulty');
        $question->points = $request->input('points');
        $question->explanation = "Difficulty: " . $request->input('difficulty');

        if ($question->type === 'MCQ') {
            $mcqOptions = $request->input('mcq_options', []);
            $question->option_a = $mcqOptions['A'] ?? null;
            $question->option_b = $mcqOptions['B'] ?? null;
            $question->option_c = $mcqOptions['C'] ?? null;
            $question->option_d = $mcqOptions['D'] ?? null;
            $question->correct_option = $request->input('mcq_correct_option');
            $question->essay_rubric = null;
        } elseif ($question->type === 'TRUE/FALSE') {
            $question->option_a = 'TRUE';
            $question->option_b = 'FALSE';
            $question->option_c = null;
            $question->option_d = null;
            $question->correct_option = $request->input('tf_correct_option');
            $question->essay_rubric = null;
        } else {
            $question->option_a = null;
            $question->option_b = null;
            $question->option_c = null;
            $question->option_d = null;
            $question->correct_option = null;
            $question->essay_rubric = $request->input('essay_rubric_guidelines');
        }

        $question->save();

        return redirect()->route('teacher.question-bank')->with('success', 'Question records updated successfully.');
    }

    /**
     * Remove the specified question model entity instance from database storage logs.
     */
    public function destroyQuestion($id)
    {
        $question = Question::findOrFail($id);

        // ✅ FIX: submission_answers.question_id has no foreign key, so
        // deleting a question a student has already answered used to
        // silently orphan their answer — the grading screen would then
        // show "No questions found" for that submission with no warning
        // of why. Block the delete instead and tell the teacher why.
        $hasAnswers = DB::table('submission_answers')->where('question_id', $id)->exists();
        if ($hasAnswers) {
            return redirect()->route('teacher.question-bank')
                ->with('error', 'This question already has student answers on record and cannot be deleted. Remove it from the exam instead if you no longer want it used.');
        }

        if (!empty($question->media_url) && file_exists(public_path($question->media_url))) {
            @unlink(public_path($question->media_url));
        }
        if (!empty($question->csv_url) && file_exists(public_path($question->csv_url))) {
            @unlink(public_path($question->csv_url));
        }
        $question->delete();

        return redirect()->route('teacher.question-bank')->with('success', 'Question record deleted.');
    }

    /**
     * Display the exam end session confirmation interface view.
     */
    public function endExamConfirmation()
    {
        return view('teacher.monitoring.end-confirmation');
    }

    /**
     * Process the finalization and database update to close the active exam session.
     */
    public function endExamSession(Request $request)
    {
        // Add your custom exam finalization DB updates here if needed.
        return redirect()->route('teacher.exam.endedOverview')->with('success', 'Exam session closed safely.');
    }

    /**
     * Display the post-exam summary overview screen after a session ends.
     */
    public function examSessionEnded()
    {
        // For now, securely redirect straight to the main dashboard workspace with a notice
        return redirect()->route('teacher.dashboard')->with('success', 'Exam session has been securely finalized.');
    }

    /**
     * Real-time cheat detection feed for the Live Proctoring Room.
     * Returns any tab-switch / fullscreen-exit violations logged by students
     * since the last poll, so the teacher sees actual detected activity
     * instead of only manually-flagged students.
     */
    public function getCheatAlerts(Request $request)
    {
        $sinceId = (int) $request->query('since_id', 0);

        $rows = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.user_id')
            ->where('audit_logs.action', 'tab_switch_violation')
            ->where('audit_logs.id', '>', $sinceId)
            ->orderBy('audit_logs.id')
            ->limit(50)
            ->get(['audit_logs.id', 'audit_logs.user_id', 'audit_logs.model_id as exam_id', 'audit_logs.payload', 'audit_logs.created_at', 'users.full_name']);

        $alerts = $rows->map(function ($row) {
            $payload = json_decode($row->payload, true) ?? [];
            return [
                'id'           => $row->id,
                'student_id'   => $row->user_id,
                'student_name' => $row->full_name ?? ('Student #' . $row->user_id),
                'exam_id'      => $row->exam_id,
                'strike_count' => $payload['strike_count'] ?? null,
                'reason'       => $payload['message'] ?? 'Tab switch / focus-loss detected.',
                'time'         => \Carbon\Carbon::parse($row->created_at)->format('H:i:s'),
            ];
        });

        $lastId = $rows->max('id') ?? $sinceId;

        return response()->json(['alerts' => $alerts, 'last_id' => $lastId]);
    }
}