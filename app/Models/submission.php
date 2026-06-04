<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exam_id', 
        'user_id', 
        'session_id', 
        'started_at', 
        'submitted_at', 
        'time_taken_seconds', 
        'status', 
        'total_score', 
        'percentage', 
        'is_passed', 
        'teacher_feedback', 
        'graded_by', 
        'graded_at',
        // 🌟 ADDED: Rubric Breakdown Metrics columns for interactive grading panels
        'accuracy_score',
        'depth_score',
        'clarity_score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at'    => 'datetime',
        'is_passed'    => 'boolean',
        'total_score'  => 'decimal:2',
        'percentage'   => 'decimal:2',
        // Authorize proper type formats for numeric processing engines
        'accuracy_score' => 'integer',
        'depth_score'    => 'integer',
        'clarity_score'  => 'integer',
    ];

    /**
     * Get the exam that owns the submission.
     */
    public function exam() {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * Get the student (user) that owns the submission.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Alias method matching your Blade view logic layout structures ($submission->student)
     */
    public function student() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the teacher who graded the submission.
     */
    public function grader() {
        return $this->belongsTo(User::class, 'graded_by', 'user_id');
    }

    /**
     * Get the session associated with the submission.
     */
    public function session() {
        return $this->belongsTo(ExamSession::class, 'session_id', 'id');
    }

    /**
     * Get the answers for the submission.
     */
    public function answers() {
        return $this->hasMany(Answer::class, 'submission_id', 'id');
    }
}