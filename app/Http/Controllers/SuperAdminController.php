<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Institution;
use App\Models\Course;
use App\Models\Exam;

class SuperAdminController extends Controller
{
    // ✅ Get all institutions
    public function institutions()
    {
        return response()->json(Institution::all());
    }

    // ✅ Create institution
    public function createInstitution(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'domain' => 'nullable|string',
        ]);

        $institution = Institution::create($data);

        return response()->json($institution, 201);
    }

    // ✅ Get all users
    public function users()
    {
        return response()->json(User::all());
    }

    // ✅ Get all courses
    public function courses()
    {
        return response()->json(Course::with('institution')->get());
    }

    // ✅ Get all exams
    public function exams()
    {
        return response()->json(Exam::with(['course', 'institution'])->get());
    }
}