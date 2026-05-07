<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Institution;
use App\Models\User;
use App\Models\Question;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'institution_id',
        'created_by',
        'subject',
        'difficulty',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    public function institution() {
        return $this->belongsTo(Institution::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions() {
        return $this->hasMany(Question::class);
    }
}