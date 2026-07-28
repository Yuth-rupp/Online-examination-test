<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Carbon; 
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Submission;
use App\Models\StudentHiddenExam;
use App\Models\AuditLog; 
use App\Events\StudentFrameSubmitted; 

class StudentController extends Controller
{
    /**
     * Handle the primary Student Web Dashboard View data generation.
     */
    public function index(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $enrolledCourseIds = Enrollment::where('user_id', $user->user_id)
            ->where('status', 'active')
            ->pluck('course_id');

        // A student's completed exams must never disappear from "assigned"
        // just because their enrollment record later lapsed or changed —
        // that's what was causing "Total Exams Assigned: 0" while
        // "Exams Completed: 2" at the same time. So the visible course set
        // is active enrollments PLUS any course the student already has a
        // submission in.
        $submittedCourseIds = Submission::where('user_id', $user->user_id)
            ->join('exams', 'submissions.exam_id', '=', 'exams.exam_id')
            ->pluck('exams.course_id');

        $accessibleCourseIds = $enrolledCourseIds->merge($submittedCourseIds)->unique()->values();

        $totalExams = Exam::whereIn('course_id', $accessibleCourseIds)
            ->where('status', 'published')
            ->count();

        $completedExams = Submission::where('user_id', $user->user_id)->count();

        $averageScore = Submission::where('user_id', $user->user_id)->avg('percentage') ?? 0;

        $submissions = Submission::with('exam')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'asc')
            ->get();

        $upcomingExams = Exam::with('course')
            ->whereIn('course_id', $accessibleCourseIds)
            ->where('status', 'published') 
            ->orderBy('start_time', 'asc')
            ->get();

        $liveExam = Exam::with('course')
            ->whereIn('course_id', $accessibleCourseIds)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->where('status', 'published')
            ->first();

        // The student's home department, plus every teacher and admin
        // currently attached to it — this is what powers the "My
        // Department" card so a student in, say, Data Science sees the
        // Data Science teachers and admins, not a generic list. Queried
        // fresh on every request (and on every poll, since the dashboard
        // is polled) so a newly assigned teacher/admin shows up without
        // the student needing to do anything.
        $department = null;
        if ($user->department_id) {
            $department = \App\Models\Department::where('id', $user->department_id)
                ->with([
                    'teachers:user_id,full_name,email,telegram_username',
                    'admins:user_id,full_name,email,telegram_username,department_id',
                ])
                ->first();
        }

        $departmentPayload = $department ? [
            'id'       => $department->id,
            'name'     => $department->name,
            'teachers' => $department->teachers->map(fn ($t) => [
                'name'              => $t->full_name,
                'email'             => $t->email,
                'telegram_username' => $t->telegram_username,
            ])->values(),
            'admins' => $department->admins->map(fn ($a) => [
                'name'              => $a->full_name,
                'email'             => $a->email,
                'telegram_username' => $a->telegram_username,
            ])->values(),
        ] : null;

        if ($request->wantsJson()) {
            return response()->json([
                'totalExams'     => $totalExams,
                'completedExams' => $completedExams,
                'averageScore'   => $averageScore,
                'upcomingExams'  => $upcomingExams,
                'liveExam'       => $liveExam,
                'submissions'    => $submissions,
                'department'     => $departmentPayload,
            ]);
        }

        return view('student.student_dashboard', compact(
            'totalExams',
            'completedExams',
            'averageScore',
            'upcomingExams',
            'liveExam',
            'submissions',
            'departmentPayload'
        ));
    }


    /**
     * Render the student's dynamic profile and exam history data panel.
     */
    public function settings(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $submissions = Submission::with('exam.course')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $averageScore = Submission::where('user_id', $user->user_id)->avg('percentage') ?? 0;

        if ($request->wantsJson()) {
            return response()->json([
                'averageScore' => $averageScore,
                'submissions'  => $submissions,
            ]);
        }

        return view('student.settings', compact('user', 'submissions', 'averageScore'));
    }

    /**
     * Save structural profile alterations from the user form panel safely.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
        ]);

        $user->update([
            'full_name' => $data['full_name']
        ]);

        \App\Models\Notification::create([
            'user_id' => $user->user_id,
            'title'   => 'Profile Updated',
            'body'    => 'Your display name was updated successfully.',
            'type'    => 'success',
        ]);

        return redirect()->route('student.settings')->with('success', 'Profile updated successfully.');
    }

    /**
     * Upload and update profile photo instantly with immediate asset preview paths.
     */
    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = $request->user() ?? Auth::user();

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            try {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Student profile photo upload failed', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->user_id,
                ]);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Upload failed: ' . $e->getMessage(),
                    ], 500);
                }

                return redirect()->back()->with('error', 'Photo upload failed: ' . $e->getMessage());
            }

            $user->update([
                'profile_image' => $path
            ]);

            \App\Models\Notification::create([
                'user_id' => $user->user_id,
                'title'   => 'Profile Photo Updated',
                'body'    => 'Your profile picture was updated successfully.',
                'type'    => 'success',
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'photo_url' => Storage::disk('public')->url($path),
                    'message' => 'Profile avatar packet written safely to disk architecture.'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Profile photo updated successfully.');
    }

    /**
     * Display the courses the authenticated student is enrolled in.
     */
    public function myCourses(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->user_id)
            ->get();

        if ($request->wantsJson() || !$request->acceptsHtml()) {
            return response()->json($enrollments);
        }

        return view('student.courses', compact('enrollments'));
    }

    /**
     * Enroll a student into a specific course.
     */
    public function enroll(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $data['user_id'] = $user->user_id;
        $data['enrolled_at'] = now();
        $data['status'] = 'active';

        $enrollment = Enrollment::create($data);

        if ($request->wantsJson()) {
            return response()->json($enrollment, 201);
        }

        return redirect()->back()->with('success', 'Enrolled into course successfully.');
    }

    /**
     * Get all available exams paired with real-time submission metrics.
     */
    public function exams(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $enrolledCourseIds = Enrollment::where('user_id', $user->user_id)
            ->where('status', 'active')
            ->pluck('course_id');

        // Same fix as the dashboard: a completed exam must stay visible
        // even if the student's enrollment for that course later lapsed —
        // otherwise the "Completed" tab and count silently drop to 0 while
        // the dashboard (which counts raw submissions) still shows them.
        $submittedCourseIds = Submission::where('user_id', $user->user_id)
            ->join('exams', 'submissions.exam_id', '=', 'exams.exam_id')
            ->pluck('exams.course_id');

        $accessibleCourseIds = $enrolledCourseIds->merge($submittedCourseIds)->unique()->values();

        $hiddenExamIds = StudentHiddenExam::where('user_id', $user->user_id)->pluck('exam_id');

        $examsQuery = Exam::with('course')
            ->whereIn('course_id', $accessibleCourseIds)
            ->whereNotIn('exam_id', $hiddenExamIds);

        if ($request->filled('course_id')) {
            $examsQuery->where('course_id', $request->input('course_id'));
        }

        $exams = $examsQuery->get();

        $submissions = Submission::where('user_id', $user->user_id)->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'exams' => $exams,
                'submissions' => $submissions
            ]);
        }

        return view('student.exams', compact('exams', 'submissions'));
    }

    /**
     * Let a student remove an exam card from their own "My Exams" list.
     * This never deletes the Exam, Submission, or grade data — it only
     * records that this student no longer wants to see it, so teachers
     * and other students are completely unaffected. Only exams that are
     * already completed (submitted, or past their end time) can be
     * dismissed, so a student can't accidentally hide an upcoming or
     * live exam they still need to take.
     */
    public function deleteExam(Request $request, $id)
    {
        $user = $request->user() ?? Auth::user();

        $exam = Exam::where('exam_id', $id)->first();

        if (!$exam) {
            return response()->json(['message' => 'Exam not found.'], 404);
        }

        $hasSubmitted = Submission::where('user_id', $user->user_id)
            ->where('exam_id', $exam->exam_id)
            ->exists();

        // A submission is proof the student legitimately took this exam,
        // regardless of what their enrollment status looks like today
        // (courses end, enrollments get archived/dropped, etc). Only fall
        // back to checking enrollment when there's no submission on record.
        if (!$hasSubmitted) {
            $enrolled = Enrollment::where('user_id', $user->user_id)
                ->where('course_id', $exam->course_id)
                ->exists();

            if (!$enrolled) {
                return response()->json(['message' => 'You are not enrolled in this exam.'], 403);
            }
        }

        $examEnded = !empty($exam->end_time) && Carbon::parse($exam->end_time)->isPast();

        if (!$hasSubmitted && !$examEnded) {
            return response()->json([
                'message' => 'Only completed exams can be removed from your list.'
            ], 422);
        }

        StudentHiddenExam::firstOrCreate([
            'user_id' => $user->user_id,
            'exam_id' => $exam->exam_id,
        ]);

        return response()->json(['message' => 'Exam removed from your list.']);
    }

    /**
     * Get the authenticated student's exam submissions history.
     */
    public function mySubmissions(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $submissions = Submission::with('exam.course', 'exam.questions')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->each(function ($sub) {
                // Real max score = sum of each question's teacher-configured
                // points on this exam — not a hardcoded 100. Falls back to
                // 5 pts/question (matching the grading screen's own
                // fallback) if points were never set.
                $questions = $sub->exam->questions ?? collect();
                $maxScore = $questions->sum('points') ?: ($questions->count() * 5) ?: 100;
                $sub->setAttribute('max_score', $maxScore);
            });

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($submissions);
        }

        return view('student.history', compact('submissions'));
    }

    /**
     * Print-ready hall ticket reference generation.
     */
    public function printHallTicket(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $institution = \App\Models\Institution::find($user->institution_id);

        $enrolledCourseIds = Enrollment::where('user_id', $user->user_id)
            ->where('status', 'active')
            ->pluck('course_id');

        $exams = Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('status', 'published')
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->get();

        $ticketNo = 'HT-' . str_pad($user->user_id, 6, '0', STR_PAD_LEFT) . '-' . now()->format('y');

        return view('student.print_ticket', compact('user', 'institution', 'exams', 'ticketNo'));
    }

    /**
     * Validate incoming classroom authorization proctor parameters.
     */
    public function enterProctorRoom(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string',
        ]);

        $exam = Exam::where('access_code', strtoupper(trim($request->access_code)))
            ->where('status', 'published')
            ->first();

        if (!$exam) {
            return redirect()->back()
                ->with('error', 'Invalid exam access code. Please check with your supervisor.')
                ->withInput();
        }

        session()->put('validated_exam_id', $exam->exam_id);

        return redirect()->route('student.exam.verification', ['code' => $exam->access_code])
            ->with('success', "Access authorized for exam: {$exam->title}");
    }

    /**
     * Render the isolation checkpoint verification holding area.
     */
    public function showVerificationPage(Request $request)
    {
        $exams = Exam::with('course')->where('status', 'published')->get();
        $sessionCode = strtoupper(trim($request->query('code')));
        $exam = Exam::where('access_code', $sessionCode)->first();

        return view('student.exam-room', compact('exams', 'exam'));
    }

    /* --- SUPPORT TICKETS SYSTEM --- */

    public function support()
    {
        $userEmail = auth()->user()->email ?? 'phatyuthyou9@gmail.com';

        $tickets = DB::table('support_tickets')
            ->where('reporter_email', $userEmail)
            ->where('status', '!=', 'resolved')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($t) {
                return [
                    'ticket_id' => $t->ticket_id,
                    'ticket_no' => $t->ticket_no,
                    'subject' => $t->issue_category,
                    'status' => strtoupper($t->status === 'in_progress' ? 'investigating' : $t->status),
                    'updated_at' => \Carbon\Carbon::parse($t->updated_at)->diffForHumans(),
                    'description' => $t->description,
                    'screenshot' => $t->screenshot,
                    'admin_comment' => $t->admin_comment
                ];
            });

        return view('student.support', compact('tickets'));
    }

    public function pollSupportNotifications()
    {
        $userEmail = auth()->user()->email ?? 'phatyuthyou9@gmail.com';

        $notifications = DB::table('support_tickets')
            ->where('reporter_email', $userEmail)
            ->where('status', '=', 'resolved')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'resolved_items' => $notifications
        ]);
    }

    public function storeSupportTicket(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'screenshot' => 'nullable|image|max:5120'
        ]);

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $path = $request->file('screenshot')->store('support_attachments', 'public');
            $screenshotPath = Storage::url($path);
        }

        $ticketNo = 'SUP-' . rand(4000, 9999);
        
        $reporterName = auth()->user()->full_name ?? 'You Phatyuth';
        $reporterEmail = auth()->user()->email ?? 'phatyuthyou9@gmail.com';

        DB::table('support_tickets')->insert([
            'ticket_no' => $ticketNo,
            'reporter_name' => $reporterName,
            'reporter_email' => $reporterEmail,
            'user_type' => 'student',
            'issue_category' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => 'high',
            'status' => 'pending', 
            'screenshot' => $screenshotPath,
            'admin_comment' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ticket = new \stdClass();
        $ticket->ticket_no = $ticketNo;
        $ticket->subject = $validated['subject'];
        $ticket->description = $validated['description'];
        $ticket->screenshot = $screenshotPath;
        $ticket->urgency = 'High (Technical)';
        $ticket->created_at = now()->format('h:i A');

        return view('student.confirm_support', compact('ticket'));
    }

    /* --- CORE ASSIGNMENT CARD ACTIONS --- */

    public function showExamDetails($id)
    {
        $exam = Exam::with('course')->findOrFail($id);
        return view('student.exams.show_details', compact('exam'));
    }

    public function enterExamInterface($id)
    {
        $exam = Exam::with(['course', 'questions'])->findOrFail($id);
        $user = Auth::user();

        if (now()->lt(Carbon::parse($exam->start_time)) || now()->gt(Carbon::parse($exam->end_time))) {
            return redirect()->route('student.exams')->with('error', 'This assessment pipeline environment is currently closed.');
        }

        return view('student.exams.live_workspace', compact('exam', 'user'));
    }

    /**
     * Render the custom student feedback report tracking metrics.
     */
    public function viewExamFeedback($id)
    {
        $user = Auth::user();
        
        /* 🛠️ FIXED: Queries precisely by individual submission key to handle multi-attempt rows */
        $submission = Submission::with('exam.course')
            ->where('id', $id) 
            ->where('user_id', $user->user_id)
            ->firstOrFail();

        return view('student.setting_feedback_report', compact('submission'));
    }

    public function startExamSession($id)
    {
        $exam = Exam::with(['course', 'questions'])->findOrFail($id);
        $now = now();
        
        $start = $exam->start_time ? Carbon::parse($exam->start_time) : $now->copy();
        
        if ($exam->end_time) {
            $end = Carbon::parse($exam->end_time);
        } else {
            $durationMinutes = $exam->duration ?? 120; 
            $end = $start->copy()->addMinutes($durationMinutes);
        }

        if ($now->gt($end)) {
            return redirect()->route('student.dashboard')->with('error', 'This assessment session window has closed.');
        }

        $secondsRemaining = (int) round($now->diffInSeconds($end, false));
        if ($secondsRemaining < 0) { $secondsRemaining = 0; }

        $ruleKeys = ['proctor_max_switches', 'proctor_warn_threshold', 'block_right_click', 'force_fullscreen', 'webcam_monitor', 'sync_interval', 'tab_switch_grace_seconds'];
        $rawRules = DB::table('system_settings')->whereIn('key', $ruleKeys)->pluck('value', 'key');
        $examRules = [
            'proctorMaxSwitches'   => (int) ($rawRules['proctor_max_switches'] ?? 3),
            'proctorWarnThreshold' => (int) ($rawRules['proctor_warn_threshold'] ?? 2),
            'blockRightClick'      => ($rawRules['block_right_click'] ?? '1') === '1',
            'forceFullscreen'      => ($rawRules['force_fullscreen'] ?? '1') === '1',
            'webcamMonitor'        => ($rawRules['webcam_monitor'] ?? '0') === '1',
            'syncInterval'         => (int) ($rawRules['sync_interval'] ?? 10),
            // Global Settings > Proctoring Thresholds > Tab-Switch Detection Grace Period.
            // How long a student can stay on another tab before it counts as a
            // violation (see setupTabDetection() in live-test.blade.php). This is a
            // Super Admin-only value — department admins cannot override it.
            'tabSwitchGrace'       => (int) ($rawRules['tab_switch_grace_seconds'] ?? 5),
        ];

        return view('student.live-test', compact('exam', 'secondsRemaining', 'examRules'));
    }

    public function logProctorViolation(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|string',
            'strike'  => 'required|integer',
        ]);

        $user = Auth::user();

        AuditLog::create([
            'user_id'        => $user->id ?? $user->user_id,
            'institution_id' => $user->institution_id ?? null,
            'action'         => 'tab_switch_violation',
            'model_type'     => 'App\Models\Exam',
            'model_id'       => $request->input('exam_id'),
            'payload'        => [
                'strike_count' => $request->input('strike'),
                'message'      => "Student shifted window focus away from active browser proctor environment."
            ],
            'ip_address'     => $request->ip(),
            'created_at'     => now(),
        ]);

        return response()->json(['status' => 'success', 'logged' => true]);
    }

    public function streamProctorFrame(Request $request)
    {
        $request->validate([
            'image_frame' => 'required|string'
        ]);

        $user = Auth::user();
        $studentName = $user->full_name ?? 'Student';

        broadcast(new StudentFrameSubmitted($studentName, $request->input('image_frame')))->toOthers();

        return response()->json(['status' => 'success', 'broadcasted' => true]);
    }

    /**
     * Process, auto-grade, and save completed student examination answers.
     *
     * FIXES APPLIED:
     * 1. Reads answers from both 'questions' AND 'answers' field names (handles
     *    whatever field name the student exam JS sends).
     * 2. Correct answer key now reads $question->correct_option (matches addQuestion()).
     *    Previously read ->correct_answer / ->answer which are always null.
     * 3. Stores answers in submission_answers so GradingController can display them.
     */
    public function storeExamSubmission(Request $request)
    {
        $user   = Auth::user();
        $examId = $request->input('exam_id');

        $exam = Exam::with('questions')->findOrFail($examId);

        // ✅ FIX 1: Accept answers from EITHER field name the JS may send
        $submittedAnswers = $request->input('questions',
                           $request->input('answers', []));

        $totalQuestions        = $exam->questions->count();
        $correctCount          = 0;
        $earnedPoints          = 0;
        $maxAutoPoints         = 0;
        $requiresManualGrading = false;

        foreach ($exam->questions as $question) {
            $chosenOption = $submittedAnswers[$question->id] ?? null;

            $qType = strtoupper($question->type ?? 'MCQ');

            if ($qType === 'ESSAY') {
                $requiresManualGrading = true;
                continue;
            }

            // Each question carries its own teacher-set point value —
            // score by that weight, not just a flat "1 per question" count.
            $questionPoints = (int) ($question->points ?? 1);
            $maxAutoPoints += $questionPoints;

            // ✅ FIX 2: Use correct_option — that's what addQuestion() saves.
            //    Old code used ->correct_answer ?? ->answer which are always null.
            $correctAnswerKey = $question->correct_option;

            if ($chosenOption !== null && $correctAnswerKey !== null) {
                $studentStr = strtolower(trim(
                    is_array($chosenOption) ? implode(',', $chosenOption) : (string)$chosenOption
                ));
                $correctStr = strtolower(trim(
                    is_array($correctAnswerKey) ? implode(',', $correctAnswerKey) : (string)$correctAnswerKey
                ));

                if ($studentStr === $correctStr) {
                    $correctCount++;
                    $earnedPoints += $questionPoints;
                }
            }
        }

        // Percentage is now earned points ÷ actual points available on this
        // exam (whatever the teacher configured per question) — not a
        // hardcoded/assumed total.
        $percentage = $maxAutoPoints > 0 ? round(($earnedPoints / $maxAutoPoints) * 100, 2) : 0;

        // ✅ FIX: is_passed was never set on this path at all, so it stayed
        // at the DB default of `false` for every fully auto-graded
        // submission (no essay questions) — regardless of real score. A
        // student who got a perfect 15/15 on an all-MCQ exam still showed
        // up as "failed" in Dashboard/Analytics pass-rate stats. Only mark
        // it here when grading is actually final (no pending essay
        // portion) — a submission still awaiting manual grading hasn't
        // earned its essay points yet, so pass/fail isn't decided until
        // GradingController::store() finishes it.
        $passMarkPercent = $exam->pass_mark ?? 50;
        $isPassed = !$requiresManualGrading && ($percentage >= $passMarkPercent);

        // Upsert exam_sessions row
        $activeSessionId = DB::table('exam_sessions')
            ->where('exam_id', $exam->exam_id)
            ->where('user_id', $user->user_id)
            ->value('id');

        if (!$activeSessionId) {
            $activeSessionId = DB::table('exam_sessions')->insertGetId([
                'exam_id'    => $exam->exam_id,
                'user_id'    => $user->user_id,
                'status'     => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create submission record
        $submission = Submission::create([
            'user_id'     => $user->user_id,
            'exam_id'     => $exam->exam_id,
            'session_id'  => $activeSessionId,
            'started_at'  => now(),
            'total_score' => $earnedPoints,
            'percentage'  => $percentage,
            'is_passed'   => $isPassed,
            'status'      => $requiresManualGrading ? 'pending_grading' : 'graded',
            'created_at'  => now(),
        ]);

        \App\Models\Notification::create([
            'user_id' => $user->user_id,
            'title'   => $requiresManualGrading ? 'Exam Submitted' : 'Exam Graded',
            'body'    => $requiresManualGrading
                ? "Your submission for \"{$exam->title}\" is awaiting manual grading."
                : "You scored {$percentage}% on \"{$exam->title}\".",
            'type'    => $requiresManualGrading ? 'warn' : 'success',
        ]);

        // ✅ FIX 3: Save every submitted answer to submission_answers
        //    GradingController::show() reads this table to display student answers.
        foreach ($submittedAnswers as $qId => $answerValue) {
            DB::table('submission_answers')->insert([
                'submission_id' => $submission->id,
                'question_id'   => $qId,
                'answer_text'   => is_array($answerValue)
                                    ? implode(',', $answerValue)
                                    : (string) $answerValue,
                'created_at'    => now(),
            ]);
        }

        return redirect()->route('student.exams.success', ['id' => $submission->id]);
    }

    public function showExamSuccess($id)
    {
        $submission = Submission::with('exam.course')->findOrFail($id);
        return view('student.exam_success', compact('submission'));
    }
}