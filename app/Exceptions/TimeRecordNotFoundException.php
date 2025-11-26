<?php

namespace App\Exceptions;

use Exception;

class TimeRecordNotFoundException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = 'TimeRecord não encontrado',
        int $code = 404,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error_code' => 'NOT_FOUND',
            'data' => null
        ], 404);
    }

    /**
     * Get the exception's context information.
     *
     * @return array
     */
    public function context(): array
    {
        return [
            'exception_type' => 'TimeRecordNotFoundException',
            'model' => 'TimeRecord',
            'timestamp' => now()->toISOString(),
        ];
    }
}