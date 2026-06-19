<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Submission;

class StudentController extends Controller
{
    /**
     * Handle the primary Student Web Dashboard View data generation.
     */
    public function index(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        // 1. Get distinct active course IDs the student is enrolled in
        $enrolledCourseIds = Enrollment::where('user_id', $user->user_id)
            ->where('status', 'active')
            ->pluck('course_id');

        // 2. Count total exams assigned to those courses
        $totalExams = Exam::whereIn('course_id', $enrolledCourseIds)->count();

        // 3. Count completed exams from submission tables
        $completedExams = Submission::where('user_id', $user->user_id)->count();

        // 4. Fixed: Changed column mapping from 'score' to 'percentage' to resolve query exception
        $averageScore = Submission::where('user_id', $user->user_id)->avg('percentage') ?? 0;

        // 5. Gather top 2 upcoming exams (where the start time is in the future)
        $upcomingExams = Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('start_time', '>', now())
            ->where('status', 'published') // Filters out drafts safely
            ->orderBy('start_time', 'asc')
            ->take(2)
            ->get();

        // 6. Look up an actively running live exam card session
        $liveExam = Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->where('status', 'published')
            ->first();

        // Check if the request expects an application/json structure response instead
        if ($request->wantsJson()) {
            return response()->json([
                'totalExams'     => $totalExams,
                'completedExams' => $completedExams,
                'averageScore'   => $averageScore,
                'upcomingExams'  => $upcomingExams,
                'liveExam'       => $liveExam
            ]);
        }

        return view('student.student_dashboard', compact(
            'totalExams',
            'completedExams',
            'averageScore',
            'upcomingExams',
            'liveExam'
        ));
    }

    /**
     * Render the student's dynamic profile and exam history data panel.
     */
    public function settings(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        // Get past graded exam performance logs for the history data table
        $submissions = Submission::with('exam.course')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate current rank and historical averages dynamically using percentage column
        $averageScore = Submission::where('user_id', $user->user_id)->avg('percentage') ?? 0;

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

        // Updates user record fields directly
        $user->update([
            'full_name' => $data['full_name']
        ]);

        return redirect()->route('student.settings')->with('success', 'Profile updated successfully.');
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
     * Get all available exams with their associated course details.
     */
    public function exams(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        // Pull active course IDs to filter exams relevant to this student's registration
        $enrolledCourseIds = Enrollment::where('user_id', $user->user_id)
            ->where('status', 'active')
            ->pluck('course_id');

        $exams = Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->get();

        // If explicitly hitting the API or requesting JSON return collection parameters
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($exams);
        }

        // Fallback interface handler to load the native Blade template requested by your web routes
        return view('student.exams', compact('exams'));
    }

    /**
     * Get the authenticated student's exam submissions history.
     */
    public function mySubmissions(Request $request)
    {
        $user = $request->user() ?? Auth::user();

        $submissions = Submission::with('exam.course')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($submissions);
        }

        // Serve history blade template view context securely
        return view('student.history', compact('submissions'));
    }

    /**
     * Print-ready structural asset engine mapping method.
     */
    public function printHallTicket(Request $request)
    {
        $user = $request->user() ?? Auth::user();
        return view('student.print_ticket', compact('user'));
    }

    /**
     * Validate incoming session access token parameters before entering proctoring phase.
     */
    public function enterProctorRoom(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string',
        ]);

        // Look up the active exam matching the single-use token code provided by the lecturer
        $exam = Exam::where('access_code', strtoupper(trim($request->access_code)))
            ->where('status', 'published')
            ->first();

        if (!$exam) {
            return redirect()->back()
                ->with('error', 'Invalid exam access code. Please request the active token parameters from your lecturer.')
                ->withInput();
        }

        // Keep a short-term check verification validation token in the session container memory
        session()->put('validated_exam_id', $exam->id);

        return redirect()->route('student.exam.verification')
            ->with('success', "Access Authorized for session: {$exam->title}");
    }

    /* --- ☎️ STUDENT DYNAMIC HELPDESK & SUPPORT FUNCTIONS --- */

    /**
     * Display static support documentation guides along with dynamic history items.
     */
    public function support()
    {
        // Simulated structural data placeholders mapping closely to UI database schemas
        $tickets = [
            [
                'ticket_no' => '#ASC-2409',
                'subject' => 'Camera calibration error',
                'status' => 'PENDING',
                'updated_at' => 'Oct 24, 10:45 AM',
                'description' => 'The proctoring system fails to calibrate my external webcam during biometric verification steps.'
            ],
            [
                'ticket_no' => '#ASC-2391',
                'subject' => 'Login credentials reset',
                'status' => 'RESOLVED',
                'updated_at' => 'Oct 22, 2:15 PM',
                'description' => 'Requested password recovery linkage context synchronization due to faculty domain migration changes.'
            ],
            [
                'ticket_no' => '#ASC-2102',
                'subject' => 'Mock exam results missing',
                'status' => 'RESOLVED',
                'updated_at' => 'Sep 15, 9:00 AM',
                'description' => 'The system summary metrics dashboard returned a blank layout matrix upon terminating the DBMS trial session.'
            ]
        ];

        return view('student.support', compact('tickets'));
    }

    /**
     * Process real-time helpdesk queries payloads and compile image download channels securely.
     */
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

        // Returns reactive dynamic state models instantly back to your front-facing layout framework 
        return response()->json([
            'status' => 'success',
            'ticket_no' => '#ASC-' . rand(3000, 9999),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'screenshot' => $screenshotPath,
            'status_badge' => 'PENDING',
            'updated_at' => now()->format('M d, h:i A')
        ]);
    }
}