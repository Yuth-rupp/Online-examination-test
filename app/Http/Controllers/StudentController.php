<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // 4. 🎯 FIXED: Changed column mapping from 'score' to 'percentage' to resolve query exception
        $averageScore = Submission::where('user_id', $user->user_id)->avg('percentage') ?? 0;

        // 5. Gather top 2 upcoming exams (where the start time is in the future)
        $upcomingExams = Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('start_time', '>', now())
            ->where('status', 'published') // assuming you filter out drafts
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
        $user = $request->user();

        return response()->json(
            Enrollment::with('course')
                ->where('user_id', $user->user_id)
                ->get()
        );
    }

    /**
     * Enroll a student into a specific course.
     */
    public function enroll(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $data['user_id'] = $request->user()->user_id;
        $data['enrolled_at'] = now();
        $data['status'] = 'active';

        $enrollment = Enrollment::create($data);

        return response()->json($enrollment, 201);
    }

    /**
     * Get all available exams with their associated course details.
     */
    public function exams(Request $request)
    {
        return response()->json(
            Exam::with('course')->get()
        );
    }

    /**
     * Get the authenticated student's exam submissions history.
     */
    public function mySubmissions(Request $request)
    {
        return response()->json(
            Submission::where('user_id', $request->user()->user_id)->get()
        );
    }
}