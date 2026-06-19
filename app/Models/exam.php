<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory;

    // Custom non-incrementing UUID settings
    public $incrementing = false;
    protected $primaryKey = 'exam_id'; 
    protected $keyType = 'string';

    protected $fillable = [
        'exam_id',
        'title',
        'course_id',
        'created_by',
        'duration',
        'pass_mark',
        'instructions',
        'status',
        'start_time',
        'end_time',
        'access_code', // CRITICAL FIX: Allows Laravel to save your single-use codes to the database!
    ];

    protected $casts = [
        'exam_id' => 'string',
        'course_id' => 'integer',
        'created_by' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Auto-generate a unique cryptographic string UUID as the primary key if not supplied.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->exam_id)) {
                $model->exam_id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relationship reference back to the course.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * Relationship reference back to the creating teacher user account.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}