<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'marks'          => 'decimal:2',
    ];

    public function exam() {
        // Tells Laravel that the foreign key and owner key are both 'exam_id'
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function questionBank() {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id', 'id');
    }

    public function answers() {
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }
}