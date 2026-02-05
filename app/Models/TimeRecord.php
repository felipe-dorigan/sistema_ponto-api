<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model de Registro de Ponto
 * 
 * Representa um registro de ponto diário de um funcionário, incluindo
 * horários de entrada, saída, intervalo e cálculos de horas trabalhadas.
 * 
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon|null $entry_time
 * @property \Illuminate\Support\Carbon|null $exit_time
 * @property \Illuminate\Support\Carbon|null $lunch_start
 * @property \Illuminate\Support\Carbon|null $lunch_end
 * @property int $worked_minutes
 * @property int $expected_minutes
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $entry_time_recorded_at
 * @property \Illuminate\Support\Carbon|null $exit_time_recorded_at
 * @property \Illuminate\Support\Carbon|null $lunch_start_recorded_at
 * @property \Illuminate\Support\Carbon|null $lunch_end_recorded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TimeRecord extends Model
{
    use HasFactory;
    // use SoftDeletes; // Descomente se quiser usar soft deletes

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'time_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'date',
        'entry_time',
        'exit_time',
        'lunch_start',
        'lunch_end',
        'worked_minutes',
        'expected_minutes',
        'notes',
        'entry_time_recorded_at',
        'exit_time_recorded_at',
        'lunch_start_recorded_at',
        'lunch_end_recorded_at'
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
        'date' => 'date',
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'lunch_start' => 'datetime',
        'lunch_end' => 'datetime',
        'entry_time_recorded_at' => 'datetime',
        'exit_time_recorded_at' => 'datetime',
        'lunch_start_recorded_at' => 'datetime',
        'lunch_end_recorded_at' => 'datetime',
        'worked_minutes' => 'integer',
        'expected_minutes' => 'integer',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ====================
    // BUSINESS LOGIC
    // ====================

    /**
     * Calcula minutos trabalhados baseado em entrada/saída/almoço
     */
    public function calculateWorkedMinutes(): int
    {
        if (!$this->entry_time || !$this->exit_time) {
            return 0;
        }

        $totalMinutes = $this->entry_time->diffInMinutes($this->exit_time);

        // Subtrai tempo de almoço
        if ($this->lunch_start && $this->lunch_end) {
            $lunchMinutes = $this->lunch_start->diffInMinutes($this->lunch_end);
            $totalMinutes -= $lunchMinutes;
        }

        return $totalMinutes;
    }

    /**
     * Retorna o saldo de minutos (trabalhado - esperado)
     */
    public function getBalanceMinutes(): int
    {
        return $this->worked_minutes - $this->expected_minutes;
    }

    /**
     * Verifica se o horário de entrada foi registrado em tempo real
     */
    public function wasEntryTimeRealTime(): bool
    {
        if (!$this->entry_time || !$this->entry_time_recorded_at) {
            return false;
        }

        $diffMinutes = $this->entry_time->diffInMinutes($this->entry_time_recorded_at);
        return $diffMinutes <= 5; // Considera tempo real se diferença <= 5min
    }

    /**
     * Verifica se o horário de saída foi registrado em tempo real
     */
    public function wasExitTimeRealTime(): bool
    {
        if (!$this->exit_time || !$this->exit_time_recorded_at) {
            return false;
        }

        $diffMinutes = $this->exit_time->diffInMinutes($this->exit_time_recorded_at);
        return $diffMinutes <= 5;
    }

    /**
     * Retorna informações de auditoria
     */
    public function getAuditInfo(): array
    {
        return [
            'entry' => [
                'time' => $this->entry_time?->format('H:i'),
                'recorded_at' => $this->entry_time_recorded_at?->format('Y-m-d H:i:s'),
                'was_real_time' => $this->wasEntryTimeRealTime()
            ],
            'exit' => [
                'time' => $this->exit_time?->format('H:i'),
                'recorded_at' => $this->exit_time_recorded_at?->format('Y-m-d H:i:s'),
                'was_real_time' => $this->wasExitTimeRealTime()
            ],
            'lunch_start' => [
                'time' => $this->lunch_start?->format('H:i'),
                'recorded_at' => $this->lunch_start_recorded_at?->format('Y-m-d H:i:s'),
            ],
            'lunch_end' => [
                'time' => $this->lunch_end?->format('H:i'),
                'recorded_at' => $this->lunch_end_recorded_at?->format('Y-m-d H:i:s'),
            ]
        ];
    }
}