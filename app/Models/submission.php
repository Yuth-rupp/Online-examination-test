<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exam;
use App\Models\User;
use App\Models\ExamSession;
use App\Models\Answer;

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
    ];

    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function grader() {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function session() {
        return $this->belongsTo(ExamSession::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }
}