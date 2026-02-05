<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Repository responsável pela persistência de dados de usuários
 * 
 * Esta classe gerencia todas as operações de banco de dados relacionadas
 * aos usuários, estendendo funcionalidades do Repository base.
 */
class UserRepository extends Repository
{
    /**
     * Construtor do repository
     * 
     * @param User $model Model de usuário injetado via DI
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Busca um usuário pelo endereço de email
     * 
     * @param string $email Email do usuário
     * @return User|null Retorna o usuário ou null se não encontrado
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
     * Conta o total de usuários cadastrados no sistema
     * 
     * @return int Número total de usuários
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