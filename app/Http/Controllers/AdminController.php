<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // ✅ THIS FIXES THE ERROR
use App\Models\User;
use App\Models\Course;
use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ✅ Get dashboard summary
    public function dashboard()
    {
        return response()->json([
            'users' => User::count(),
            'courses' => Course::count(),
            'exams' => Exam::count(),
            'question_banks' => QuestionBank::count(),
        ]);
    }

    // ✅ Get all users
    public function users()
    {
        return response()->json(User::latest()->get());
    }

    // ✅ Create user
    public function createUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|string',
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    // ✅ Delete user
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    // ✅ Get all courses
    public function courses()
    {
        return response()->json(Course::with('institution')->get());
    }

    // ✅ Create course
    public function createCourse(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $course = Course::create($data);

        return response()->json($course, 201);
    }

    // ✅ Get all exams
    public function exams()
    {
        return response()->json(
            Exam::with(['course', 'creator'])->get()
        );
    }

    // ✅ Get reports
    public function reports()
    {
        return response()->json(Report::latest()->get());
    }
}