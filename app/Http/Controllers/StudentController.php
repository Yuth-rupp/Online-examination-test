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
    // ✅ Get my courses
    public function myCourses(Request $request)
    {
        $user = $request->user();

        return response()->json(
            Enrollment::with('course')
                ->where('user_id', $user->id)
                ->get()
        );
    }

    // ✅ Enroll in course
    public function enroll(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $data['user_id'] = $request->user()->id;
        $data['enrolled_at'] = now();

        $enrollment = Enrollment::create($data);

        return response()->json($enrollment, 201);
    }

    // ✅ Get available exams
    public function exams(Request $request)
    {
        return response()->json(
            Exam::with('course')->get()
        );
    }

    // ✅ My submissions
    public function mySubmissions(Request $request)
    {
        return response()->json(
            Submission::where('user_id', $request->user()->id)->get()
        );
    }
}