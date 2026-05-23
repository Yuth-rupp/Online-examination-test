<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'user_id', 'ip_address', 'user_agent',
        'status', 'joined_at', 'left_at', 'flags'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'flags' => 'array'
    ];

    public function exam() {
        // Maps the session tracking point back to your custom exam UUID primary column
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function submission() {
        return $this->hasOne(Submission::class, 'session_id', 'id');
    }
}