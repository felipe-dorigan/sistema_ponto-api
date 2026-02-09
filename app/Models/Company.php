<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Company
 * 
 * Representa uma empresa/organização no sistema multi-tenant.
 * Cada empresa pode ter múltiplos usuários e registros de ponto.
 * 
 * @property int $id
 * @property string $name
 * @property string $cnpj
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip_code
 * @property int $max_users
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Company extends Model
{
    use HasFactory;
    // use SoftDeletes; // Descomente se quiser usar soft deletes

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'max_users',
        'active'
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
        'max_users' => 'integer',
        'active' => 'boolean',
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
     * Usuários pertencentes a esta empresa
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Registros de ponto dos usuários desta empresa
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function timeRecords()
    {
        return $this->hasManyThrough(TimeRecord::class, User::class);
    }

    // ====================
    // SCOPES
    // ====================

    /**
     * Scope para filtrar apenas empresas ativas
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para filtrar empresas por CNPJ
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $cnpj
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCnpj($query, string $cnpj)
    {
        return $query->where('cnpj', $cnpj);
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