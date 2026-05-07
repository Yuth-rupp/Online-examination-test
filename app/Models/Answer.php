<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Submission;
use App\Models\Question;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id', 'question_id', 'answer_content',
        'is_correct', 'marks_awarded', 'teacher_comment'
    ];

    protected $casts = [
        'answer_content' => 'array',
        'is_correct' => 'boolean'
    ];

    public function submission() {
        return $this->belongsTo(Submission::class);
    }

    public function question() {
        return $this->belongsTo(Question::class);
    }
}