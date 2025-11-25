<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    use HasFactory;

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
    ];

    protected $casts = [
        'date' => 'date',
        'entry_time' => 'datetime:H:i',
        'exit_time' => 'datetime:H:i',
        'lunch_start' => 'datetime:H:i',
        'lunch_end' => 'datetime:H:i',
    ];

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calcula os minutos trabalhados
     */
    public function calculateWorkedMinutes()
    {
        if (!$this->entry_time || !$this->exit_time) {
            return 0;
        }

        $entry = strtotime($this->entry_time);
        $exit = strtotime($this->exit_time);
        $totalMinutes = ($exit - $entry) / 60;

        // Subtrai o tempo de almoço se existir
        if ($this->lunch_start && $this->lunch_end) {
            $lunchStart = strtotime($this->lunch_start);
            $lunchEnd = strtotime($this->lunch_end);
            $lunchMinutes = ($lunchEnd - $lunchStart) / 60;
            $totalMinutes -= $lunchMinutes;
        }

        return max(0, $totalMinutes);
    }

    /**
     * Calcula o saldo de horas (positivo ou negativo)
     */
    public function getBalanceMinutes()
    {
        return $this->worked_minutes - $this->expected_minutes;
    }
}
