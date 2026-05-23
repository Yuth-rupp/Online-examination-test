<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Submission;

class StudentController extends Controller
{
    /**
     * Display the courses the authenticated student is enrolled in.
     */
    public function myCourses(Request $request)
    {
        $user = $request->user();

        // Fixed to query against your custom primary key: user_id
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
        // Validates incoming data against the auto-incrementing 'id' column on the courses table
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
        // Fixed to reference user_id instead of default id
        return response()->json(
            Submission::where('user_id', $request->user()->user_id)->get()
        );
    }
}