<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'user_id', 'session_id', 'started_at', 'submitted_at', 
        'time_taken_seconds', 'status', 'total_score', 'percentage', 
        'is_passed', 'teacher_feedback', 'graded_by', 'graded_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at'    => 'datetime',
        'is_passed'    => 'boolean',
        'total_score'  => 'decimal:2',
        'percentage'   => 'decimal:2',
    ];

    public function exam() {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function grader() {
        return $this->belongsTo(User::class, 'graded_by', 'user_id');
    }

    public function session() {
        return $this->belongsTo(ExamSession::class, 'session_id', 'id');
    }

    public function answers() {
        return $this->hasMany(Answer::class, 'submission_id', 'id');
    }
}