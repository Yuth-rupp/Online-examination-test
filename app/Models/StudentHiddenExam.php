<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentHiddenExam extends Model
{
    protected $table = 'student_hidden_exams';

    protected $fillable = [
        'user_id',
        'exam_id',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}