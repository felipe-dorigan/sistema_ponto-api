<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'daily_work_hours',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relacionamento com registros de ponto
     */
    public function timeRecords()
    {
        return $this->hasMany(TimeRecord::class);
    }

    /**
     * Relacionamento com ausências
     */
    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Calcula o banco de horas total do usuário
     */
    public function getTotalHoursBalance()
    {
        $totalMinutes = $this->timeRecords()
            ->sum('worked_minutes');
        
        $expectedMinutes = $this->timeRecords()
            ->sum('expected_minutes');

        return ($totalMinutes - $expectedMinutes) / 60; // retorna em horas
    }
}
