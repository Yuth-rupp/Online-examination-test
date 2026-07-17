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

    /**
     * The table associated with the model data layer.
     * Explicitly defining this ensures Laravel links to 'question_banks' table correctly.
     *
     * @var string
     */
    protected $table = 'question_banks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'institution_id',
        'created_by',
        'subject',
        'difficulty',
        'tags'
    ];

    /**
     * The attributes that should be cast to native database types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array'
    ];

    /**
     * Get the institution that owns this master question collection bank workspace.
     */
    public function institution() 
    {
        return $this->belongsTo(Institution::class, 'institution_id', 'id');
    }

    /**
     * Get the faculty member user who created this collection partition container.
     */
    public function creator() 
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the child question entities stored inside this specific collection room bank.
     */
    public function questions() 
    {
        // Links smoothly down to the question matching reference row column field
        return $this->hasMany(Question::class, 'question_bank_id', 'id');
    }
}