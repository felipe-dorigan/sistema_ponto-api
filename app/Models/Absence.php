<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model de Ausência
 * 
 * Representa uma ausência de um funcionário, como falta, atestado,
 * férias ou licença médica.
 * 
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon $end_time
 * @property string $reason
 * @property string $description
 * @property string $status
 * @property string $impact_type
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Absence extends Model
{
    use HasFactory;
    // use SoftDeletes; // Descomente se quiser usar soft deletes

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'absences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'reason',
        'description',
        'status',
        'impact_type',
        'approved_by',
        'approved_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
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
     * Relacionamento com o usuário que possui a ausência
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacionamento com o usuário que aprovou a ausência
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ====================
    // BUSINESS LOGIC
    // ====================

    /**
     * Calcula duração da ausência em minutos
     */
    public function getDurationMinutes(): int
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Verifica se está pendente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verifica se foi aprovada
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Verifica se foi rejeitada
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Aprova a ausência
     */
    public function approve(int $userId): bool
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        return $this->save();
    }

    /**
     * Rejeita a ausência
     */
    public function reject(int $userId): bool
    {
        $this->status = 'rejected';
        $this->approved_by = $userId;
        $this->approved_at = now();
        return $this->save();
    }

    // ====================
    // SCOPES
    // ====================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}