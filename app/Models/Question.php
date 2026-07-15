<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $fillable = [
        'exam_id', 
        'question_bank_id', 
        'course_id',      
        'type', 
        'difficulty',     
        'content', 
        'option_a',       
        'option_b',       
        'option_c',       
        'option_d',       
        'correct_option', 
        'essay_rubric',   
        'options',        
        'correct_answer', 
        'points',         
        'explanation', 
        'media_url',
        'csv_url',
        'original_filename',
    ];

    protected $casts = [
        'options'         => 'array',
        'correct_answer'  => 'array',
        'points'          => 'integer',
    ];

    /**
     * Get the question bank context that owns the question.
     */
    public function questionBank() 
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id', 'id');
    }
}