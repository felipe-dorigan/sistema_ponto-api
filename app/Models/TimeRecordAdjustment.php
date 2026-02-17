<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model TimeRecordAdjustment
 * 
 * [Adicione descrição do modelo aqui]
 * 
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TimeRecordAdjustment extends Model
{
    use HasFactory;
    // use SoftDeletes; // Descomente se quiser usar soft deletes

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'time_record_adjustments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'time_record_id',
        'user_id',
        'field_to_change',
        'current_value',
        'requested_value',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    // ====================
    // RELATIONSHIPS
    // ====================

    /**
     * Registro de ponto que está sendo ajustado
     */
    public function timeRecord()
    {
        return $this->belongsTo(TimeRecord::class, 'time_record_id');
    }

    /**
     * Usuário que solicitou o ajuste
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Administrador que revisou a solicitação
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ====================
    // SCOPES
    // ====================

    /**
     * Scope para filtrar solicitações pendentes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para filtrar solicitações aprovadas
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope para filtrar solicitações rejeitadas
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope para filtrar por usuário
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ====================
    // ACCESSORS & MUTATORS
    // ====================

    // Adicione seus accessors e mutators aqui
    // Exemplo:
    // protected function name(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn (string $value) => ucfirst($value),
    //         set: fn (string $value) => strtolower($value),
    //     );
    // }
}