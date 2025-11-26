<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Login do usuário e geração do token JWT
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
     * Retorna os dados do usuário logado
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    /**
     * Faz logout (invalida o token)
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    /**
     * Estrutura padrão da resposta com token
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
