<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserLimitExceededException;
use App\Models\ApiLog;

class Handler extends ExceptionHandler
{
    /**
     * Lista de exceções que não são reportadas.
     */
    protected $dontReport = [
        //
    ];

    /**
     * Inputs que nunca devem aparecer em erros de validação.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Renderiza exceções em uma resposta HTTP.
     */
    public function render($request, Throwable $exception)
    {
        $level = 'error';
        $status = 500;
        $msg = 'Erro interno do servidor';
        $data = null;

        if ($exception instanceof AuthenticationException) {
            $level = 'warning';
            $status = 401;
            $msg = 'Não autenticado. Por favor, forneça um token válido.';
        } elseif ($exception instanceof ValidationException) {
            $level = 'info';
            $status = 422;
            $msg = 'Os dados fornecidos são inválidos.';
            $data = $exception->errors(); // Retorna um objeto com os erros de cada campo
        } elseif ($exception instanceof ModelNotFoundException) {
            $level = 'notice';
            $status = 404;
            $msg = 'O registro solicitado não foi encontrado.';
        } elseif ($exception instanceof UserNotFoundException) {
            $level = 'notice';
            $status = 404;
            $msg = $exception->getMessage();
        } elseif ($exception instanceof UserLimitExceededException) {
            $level = 'warning';
            $status = 422;
            $msg = $exception->getMessage();
        } elseif ($exception instanceof TokenInvalidException) {
            $level = 'warning';
            $status = 401;
            $msg = 'Token inválido.';
        } elseif ($exception instanceof TokenExpiredException) {
            $level = 'warning';
            $status = 401;
            $msg = 'Token expirado.';
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $level = 'warning';
            $status = 403;
            $msg = 'Acesso negado.';
        } elseif ($exception instanceof NotFoundHttpException) {
            $level = 'notice';
            $status = 404;
            $msg = 'Rota não encontrada.';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $level = 'warning';
            $status = 405;
            $msg = 'O método ' . $request->method() . ' não é permitido para esta rota.';
        }

        $logData = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'input' => $request->all(),
            'exception' => get_class($exception),
            'message' => (config('app.debug') && $msg) ? $msg : $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ];

        ApiLog::create(array_merge($logData, ['level' => $level]));

        // Se houver dados de erro (como na validação), inclua-os na resposta
        if ($data) {
            return ApiResponse::error($msg, $status, $data);
        }

        return ApiResponse::error(
            (config('app.debug') && $msg) ? $msg : $exception->getMessage(),
            $status
        );
    }
}
