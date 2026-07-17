<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Course;
use App\Models\Question; // Imported to prevent RelationNotFoundException errors

class Exam extends Model
{
    use HasFactory;

    // Custom non-incrementing UUID layout parameters
    public $incrementing = false;
    protected $primaryKey = 'exam_id'; 
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'access_code',
    ];

    /**
     * The attributes that should be cast to native database types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exam_id'    => 'string',
        'course_id'  => 'integer',
        'created_by' => 'integer',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
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
     * ACCESSOR: Maps $exam->duration_minutes smoothly to your column database keys
     */
    public function getDurationMinutesAttribute()
    {
        return $this->attributes['duration'] ?? 120;
    }

    /**
     * MUTATOR: Set duration metrics
     */
    public function setDurationMinutesAttribute($value)
    {
        $this->attributes['duration'] = $value;
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

    /**
     * Relationship reference to pull questionnaire items.
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'exam_id', 'exam_id');
    }
}