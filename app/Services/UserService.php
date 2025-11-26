<?php

namespace App\Services;

use App\DTO\UserDTO;
use App\Repositories\UserRepository;
use App\Exceptions\UserLimitExceededException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserService
{
    public function __construct(protected UserRepository $userRepository)
    {
    }

    public function incluir(UserDTO $dto)
    {
        try {
            return DB::transaction(function () use ($dto) {
                // 1. Verifica se há limite de usuários (regra de negócio)
                $totalUsers = $this->userRepository->contarUsuarios();
                if ($totalUsers >= 1000) {
                    throw new UserLimitExceededException(1000);
                }

                // 2. Transforma o DTO em um array. A criptografia será feita pelo Model.
                $dados = [
                    'name' => $dto->name,
                    'email' => $dto->email,
                    'password' => $dto->password,
                ];

                // 3. Cria o usuário
                $user = $this->userRepository->incluir($dados);

                // 4. Aqui você pode adicionar outras operações que fazem parte da transação
                // como criar perfil padrão, enviar notificações, etc.

                return $user;
            });

        } catch (UserLimitExceededException $e) {
            // Re-lança exceções de negócio específicas
            throw $e;

        } catch (\Exception $e) {
            // Loga o erro com contexto
            Log::error('Erro ao criar usuário', [
                'dto' => [
                    'name' => $dto->name,
                    'email' => $dto->email,
                    // Não loga a senha por segurança
                ],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lança a exceção para ser tratada no Controller
            throw $e;
        }
    }

    public function atualizar(int $id, array $dados)
    {
        try {
            return DB::transaction(function () use ($id, $dados) {
                // Se houver password, vamos garantir que seja hasheado
                if (isset($dados['password']) && !empty($dados['password'])) {
                    $dados['password'] = bcrypt($dados['password']);
                }

                $updatedUser = $this->userRepository->atualizar($id, $dados);

                if (!$updatedUser) {
                    Log::warning('Tentativa de atualizar usuário inexistente', ['user_id' => $id]);
                }

                return $updatedUser;
            });

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário', [
                'user_id' => $id,
                'dados' => $dados,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function obterPorCodigo(int $id)
    {
        return $this->userRepository->obterPorCodigo($id);
    }

    public function listar()
    {
        try {
            return $this->userRepository->listar();

        } catch (\Exception $e) {
            Log::error('Erro ao listar usuários', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function excluir(int $id)
    {
        try {
            $result = $this->userRepository->excluir($id);

            if (!$result) {
                Log::warning('Tentativa de excluir usuário inexistente', ['user_id' => $id]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Erro ao excluir usuário', [
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}