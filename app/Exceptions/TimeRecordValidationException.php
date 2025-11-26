<?php

namespace App\Exceptions;

use Exception;

class TimeRecordValidationException extends Exception
{
    protected array $errors;

    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param array $errors
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = 'Dados de TimeRecord inválidos',
        array $errors = [],
        int $code = 422,
        ?Exception $previous = null
    ) {
        $this->errors = $errors;
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
            'error_code' => 'VALIDATION_ERROR',
            'errors' => $this->errors,
            'data' => null
        ], 422);
    }

    /**
     * Get the validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set the validation errors.
     *
     * @param array $errors
     * @return self
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Get the exception's context information.
     *
     * @return array
     */
    public function context(): array
    {
        return [
            'exception_type' => 'TimeRecordValidationException',
            'model' => 'TimeRecord',
            'validation_errors' => $this->errors,
            'timestamp' => now()->toISOString(),
        ];
    }
}