<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsável pela autenticação de usuários
 * 
 * Gerencia login, logout e operações relacionadas a tokens JWT.
 */
class AuthController extends Controller
{
    /**
     * Realiza login do usuário e gera token JWT
     * 
     * @param Request $request Requisição contendo email e password
     * @return \Illuminate\Http\JsonResponse Token de acesso ou erro de autenticação
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Retorna os dados do usuário autenticado
     * 
     * @return \Illuminate\Http\JsonResponse Dados do usuário logado
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    /**
     * Realiza logout do usuário e invalida o token JWT
     * 
     * @return \Illuminate\Http\JsonResponse Mensagem de sucesso
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    /**
     * Formata a resposta contendo o token JWT
     * 
     * @param string $token Token JWT gerado
     * @return \Illuminate\Http\JsonResponse Resposta formatada com token e metadados
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 // em segundos
        ]);
    }
}
