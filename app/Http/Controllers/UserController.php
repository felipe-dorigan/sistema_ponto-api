<?php

namespace App\Http\Controllers;

use App\DTO\UserDTO;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function incluir(UserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->incluir(
                UserDTO::fromRequest($request->validated())
            );
            return response()->json(new UserResource($user), 201);
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Erro específico de banco (ex: violação de constraint)
            return response()->json([
                'message' => 'Erro ao salvar usuário. Verifique os dados enviados.',
                'error' => config('app.debug') ? $e->getMessage() : 'Database error'
            ], 422);
            
        } catch (\Exception $e) {
            // Erro genérico não previsto
            return response()->json([
                'message' => 'Erro interno do servidor.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function atualizar(UserRequest $request, User $user): JsonResponse
    {
        try {
            $updatedUser = $this->userService->atualizar(
                $user->id,
                $request->validated() // Passa apenas os dados validados como array
            );
            
            if (!$updatedUser) {
                return response()->json(['message' => 'Usuário não encontrado.'], 404);
            }
            
            return response()->json(new UserResource($updatedUser));
            
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Erro ao atualizar usuário no banco de dados.',
                'error' => config('app.debug') ? $e->getMessage() : 'Database error'
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar usuário.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function obterPorCodigo(User $user)
    {
        // O Model Binding do Laravel já busca o usuário para nós.
        return new UserResource($user);
    }

    public function listar(): JsonResponse
    {
        try {
            $users = $this->userService->listar();
            // Preserva a estrutura de paginação ao usar Resource::collection
            return response()->json([
                'data' => UserResource::collection($users->items()),
                'links' => [
                    'first' => $users->url(1),
                    'last' => $users->url($users->lastPage()),
                    'prev' => $users->previousPageUrl(),
                    'next' => $users->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar usuários.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function excluir(User $user): JsonResponse
    {
        try {
            $this->userService->excluir($user->id);
            return response()->json(null, 204);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir usuário.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
