<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exam;
use App\Models\QuestionBank;
use App\Models\Answer;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'question_bank_id', 'type', 'content', 'options',
        'correct_answer', 'marks', 'order', 'explanation', 'media_url',
    ];

    protected $casts = [
        'options'        => 'array',
        'correct_answer' => 'array',
    ];

    public function exam() {
        return $this->belongsTo(Exam::class);
    }

    public function questionBank() {
        return $this->belongsTo(QuestionBank::class);
    }

    public function answers() {
        return $this->hasMany(Answer::class);
    }
}