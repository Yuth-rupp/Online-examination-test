<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Institution;
use App\Models\User;
use App\Models\Exam;
use App\Models\Enrollment;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'institution_id', 'teacher_id', 'is_active'
    ];

    public function institution() {
        return $this->belongsTo(Institution::class);
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function exams() {
        return $this->hasMany(Exam::class);
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class);
    }
}