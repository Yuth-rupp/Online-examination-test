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

    /**
     * Get the exam this question belongs to (used to trace the owning teacher,
     * since questions don't store created_by directly).
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    /**
     * A working URL for the attached image, regardless of how it was stored.
     * Legacy rows have media_url like 'uploads/questions/xxx.jpg', saved
     * directly under public/ — those are served with asset(). Newer rows
     * are relative paths on the 'public' Storage disk (e.g.
     * 'question_media/xxx.jpg'), which resolves to local storage or
     * Cloudflare R2 depending on the FILESYSTEM_PUBLIC_DRIVER env var.
     */
    public function getMediaFullUrlAttribute(): ?string
    {
        return $this->resolveFileUrl($this->media_url);
    }

    public function getCsvFullUrlAttribute(): ?string
    {
        return $this->resolveFileUrl($this->csv_url);
    }

    private function resolveFileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    }
}