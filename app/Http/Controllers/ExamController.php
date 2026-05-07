<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // This function handles Route::get('/exams', ...)
    public function index()
    {
        // Fetch all exams from your SQL 'exams' table
        $exams = Exam::all();
        return response()->json($exams);
    }

    // This function handles Route::post('/exams/{id}/submit', ...)
    public function submit(Request $request, $id)
    {
        // We will add the scoring logic here later!
        return response()->json(['message' => 'Exam submitted successfully']);
    }
}