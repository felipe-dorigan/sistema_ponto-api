<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Retorno de sucesso
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
     * Retorno de erro
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
