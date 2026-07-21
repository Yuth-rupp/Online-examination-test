<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Institution;
class AuditLog extends Model
{
    use HasFactory;
    /**
     * Disable standard Eloquent automated created_at/updated_at timestamp engines.
     * Your migration manually handles custom database timestamp logging arrays.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audit_logs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'institution_id',
        'action',
        'model_type',
        'model_id',
        'payload',
        'ip_address',
        'created_at'
    ];
    /**
     * The attributes that should be cast to native database types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload'    => 'array',
        'created_at' => 'datetime'
    ];
    /**
     * Relationship reference back to the targeted User model account state.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    /**
     * Relationship reference back to the managing Institution corporate workspace entity scope.
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id', 'id');
    }
}