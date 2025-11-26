<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserRepository extends Repository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Método específico do UserRepository que não existe no Repository.
     */
    public function obterPorEmail(string $email): ?User
    {
        try {
            return $this->model->where('email', $email)->first();
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Erro ao buscar usuário por email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            // Para operações de leitura, pode retornar null em vez de quebrar
            return null;
        }
    }
    
    /**
     * Conta o total de usuários no sistema.
     */
    public function contarUsuarios(): int
    {
        try {
            return $this->model->count();
            
        } catch (\Exception $e) {
            Log::error('Erro ao contar usuários', ['error' => $e->getMessage()]);
            return 0; // Valor padrão seguro
        }
    }
}