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

        // 4. Calculate average score matching the total_score column in your migrations/seeder
        $averageScore = Submission::where('user_id', $user->user_id)->avg('total_score') ?? 0;

        // 5. Gather top 2 upcoming exams (where the start time is in the future)
        $upcomingExams = Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('start_time', '>', now())
            ->where('status', 'published') 
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