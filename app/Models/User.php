<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;

/**
 * Model de Usuário
 * 
 * Representa um usuário/funcionário do sistema de controle de ponto.
 * Implementa autenticação JWT e gerencia relacionamentos com registros
 * de ponto e ausências.
 * 
 * @property int $id
 * @property int|null $company_id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $hire_date
 * @property int $daily_work_hours
 * @property int $lunch_duration
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'role',
        'hire_date',
        'daily_work_hours',
        'lunch_duration',
        'active',
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
        'hire_date' => 'date',
        'daily_work_hours' => 'integer',
        'lunch_duration' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * Accessor/Mutator para senha
     * 
     * Automaticamente criptografa a senha ao ser definida.
     *
     * @return Attribute Atributo configurado para criptografar senha
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::make($value),
        );
    }

    /**
     * Obtém o identificador que será armazenado no subject claim do JWT
     * 
     * @return mixed Identificador do usuário (geralmente o ID)
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // geralmente o id do usuário
    }

    /**
     * Retorna um array de claims customizados para o JWT
     * 
     * @return array Claims adicionais para o token
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // ====================
    // RELATIONSHIPS
    // ====================

    /**
     * Relacionamento com registros de ponto
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeRecords()
    {
        return $this->hasMany(TimeRecord::class);
    }

    /**
     * Relacionamento com ausências
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    // ====================
    // HELPER METHODS
    // ====================

    /**
     * Verifica se o usuário tem perfil de administrador
     * 
     * @return bool True se for admin, false caso contrário
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Calcula o saldo total de horas do usuário (banco de horas)
     * 
     * @return float Saldo de horas (positivo = crédito, negativo = débito)
     */
    public function getTotalHoursBalance(): float
    {
        $totalWorkedMinutes = $this->timeRecords()->sum('worked_minutes');
        $totalExpectedMinutes = $this->timeRecords()->sum('expected_minutes');

        return ($totalWorkedMinutes - $totalExpectedMinutes) / 60; // retorna em horas
    }
}
