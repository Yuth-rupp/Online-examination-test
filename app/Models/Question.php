<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exam_id', 
        'question_bank_id', 
        'course_id',      // Added to support course categorization drop selections
        'type', 
        'difficulty',     // Added to support Easy, Medium, Hard string definitions
        'content', 
        'options',
        'correct_answer', 
        'marks', 
        'order', 
        'explanation', 
        'media_url',
        'tags',           // Added to support your topic chapter taxonomy tags array
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options'        => 'array',
        'correct_answer' => 'array',
        'tags'           => 'array', // Cast tags array to handle json conversions natively
        'marks'          => 'decimal:2',
    ];

    /**
     * Get the exam that owns the question.
     */
    public function exam() {
        // Tells Laravel that the foreign key and owner key are both 'exam_id'
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * Get the question bank context that owns the question.
     */
    public function questionBank() {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id', 'id');
    }

    /**
     * Get the student response answers linked to this question.
     */
    public function answers() {
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }
}