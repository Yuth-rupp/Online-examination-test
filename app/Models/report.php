<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Institution; // 👈 Already there
use App\Models\User;        // 🌟 ADD THIS MISSING LINE HERE 🌟

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'generated_by',
        'type',
        'payload',
        'period_start',
        'period_end'
    ];

    protected $casts = [
        'payload' => 'array',
        'period_start' => 'date',
        'period_end' => 'date'
    ];

    public function institution() 
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator() 
    {
        return $this->belongsTo(User::class, 'generated_by', 'user_id');
    }
}