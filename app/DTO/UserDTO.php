<?php

namespace App\DTO;

use App\Http\Requests\User\UserRequest;

/**
 * Data Transfer Object para usuários
 * 
 * Este DTO encapsula os dados de usuário transportados entre camadas,
 * garantindo imutabilidade e type-safety.
 */
class UserDTO
{
    /**
     * Construtor do DTO
     * 
     * @param string $name Nome do usuário
     * @param string $email Email do usuário
     * @param string|null $password Senha do usuário (opcional)
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null
    ) {
    }

    /**
     * Cria uma instância do DTO a partir de dados validados
     * 
     * @param array $validated Array de dados validados da requisição
     * @return self Nova instância do DTO
     */
    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'] ?? '',
            email: $validated['email'] ?? '',
            password: $validated['password'] ?? null
        );
    }

    /**
     * Cria uma instância do DTO a partir de dados de atualização
     * 
     * @param array $validated Array de dados validados da requisição de atualização
     * @return self Nova instância do DTO
     */
    public static function fromUpdateRequest(array $validated): self
    {
        return new self(
            name: $validated['name'] ?? '',
            email: $validated['email'] ?? '',
            password: $validated['password'] ?? null
        );
    }
}