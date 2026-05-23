<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Submission;

class TeacherController extends Controller
{
    // ✅ My courses
    public function myCourses(Request $request)
    {
        return response()->json(
            Course::where('teacher_id', $request->user()->user_id)->get()
        );
    }

    // ✅ Create exam 
    public function createExam(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'duration' => 'required|integer', 
            'pass_mark' => 'required|numeric'
        ]);

        $data['created_by'] = $request->user()->user_id;

        $exam = Exam::create($data);

        return response()->json($exam, 201);
    }

    // ✅ Add question (Matches your UUID string requirement for exam reference mapping)
    public function addQuestion(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|string', 
            'content' => 'required|string',
            'type' => 'required|string',
            'marks' => 'required|numeric',
            'options' => 'required|array',
            'correct_answer' => 'required|array',
        ]);
        // 🛠️ Add this line to temporarily turn off checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Question::create($data);
        
        // 🛠️ Add this line to turn them back on safely
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return redirect()->back()->with('success', 'Question added successfully to the exam database layout!');
    }

    // ✅ View submissions for an exam
    public function submissions($examId)
    {
        return response()->json(
            Submission::where('exam_id', $examId)->get()
        );
    }
}