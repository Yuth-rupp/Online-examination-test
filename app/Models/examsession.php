<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exam;
use App\Models\User;
use App\Models\Submission;

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
        return $this->belongsTo(Exam::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function submission() {
        return $this->hasOne(Submission::class, 'session_id');
    }
}