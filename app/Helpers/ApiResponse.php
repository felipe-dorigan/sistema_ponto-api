<?php

namespace App\Helpers;

/**
 * Helper para padronização de respostas da API
 * 
 * Esta classe fornece métodos estáticos para criar respostas JSON
 * padronizadas de sucesso e erro.
 */
class ApiResponse
{
    /**
     * Retorna uma resposta de sucesso padronizada
     * 
     * @param mixed $data Dados a serem retornados
     * @param string $message Mensagem de sucesso
     * @param int $status Código HTTP de status (padrão: 200)
     * @return \Illuminate\Http\JsonResponse Resposta JSON formatada
     */
    public static function success($data = [], $message = 'Operação realizada com sucesso', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status' => $status,
        ], $status);
    }

    /**
     * Retorna uma resposta de erro padronizada
     * 
     * @param string $message Mensagem de erro
     * @param int $status Código HTTP de status (padrão: 400)
     * @param mixed $data Dados adicionais do erro (opcional)
     * @return \Illuminate\Http\JsonResponse Resposta JSON formatada
     */
    public static function error($message = 'Ocorreu um erro', $status = 400, $data = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'status' => $status,
        ], $status);
    }
}
