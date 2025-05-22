<?php
// app/Exceptions/Handler.php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        // keep your existing callbacks...
    }

    /**
     * Override the default render so we can catch InsufficientFundsException
     * before it bubbles up as a 500.
     */
    public function render($request, Throwable $e)
    {
        // Validation exceptions (422) still go through invalidJson()
        if ($e instanceof ValidationException && $request->wantsJson()) {
            return $this->invalidJson($request, $e);
        }

        // Eloquent model not found → 404
        if ($e instanceof ModelNotFoundException) {
            return $this->error(
                'Resource not found',
                [],
                404
            );
        }

        // Route not found → 404
        if ($e instanceof NotFoundHttpException) {
            return $this->error(
                $e->getMessage() ?: 'Not Found',
                [],
                404
            );
        }

        if ($e instanceof InsufficientFundsException) {
            return $this->error(
                $e->getMessage(),
                [],
                409
            );
        }

        // Fallback to the parent (this will produce a 500)
        return parent::render($request, $e);
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $exception->errors(),
        ], $exception->status);
    }
}
