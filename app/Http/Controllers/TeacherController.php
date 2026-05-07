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
            Course::where('teacher_id', $request->user()->id)->get()
        );
    }

    // ✅ Create exam
    public function createExam(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'duration_minutes' => 'required|integer',
        ]);

        $data['created_by'] = $request->user()->id;

        $exam = Exam::create($data);

        return response()->json($exam, 201);
    }

    // ✅ Add question
    public function addQuestion(Request $request)
    {
        $data = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'content' => 'required|string',
            'type' => 'required|string',
            'marks' => 'required|integer',
        ]);

        $question = Question::create($data);

        return response()->json($question, 201);
    }

    // ✅ View submissions for an exam
    public function submissions($examId)
    {
        return response()->json(
            Submission::where('exam_id', $examId)->with('user')->get()
        );
    }
}