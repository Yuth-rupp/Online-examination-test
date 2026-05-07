<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Institution;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'institution_id', 'action', 'model_type',
        'model_id', 'payload', 'ip_address', 'created_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function institution() {
        return $this->belongsTo(Institution::class);
    }
}