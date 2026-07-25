<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPasswordResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'message',
        'status',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * The admin account this request was raised for.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * The Super Admin who resolved (reset the password for) this request.
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by', 'user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}