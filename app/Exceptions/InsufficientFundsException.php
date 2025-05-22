<?php

namespace App\Exceptions;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Throwable;

/**
 * Thrown when a user/account doesn’t have enough balance to complete an operation.
 */
class InsufficientFundsException extends \RuntimeException implements Responsable
{
    /**
     * @param string|null $message
     * @param int $code HTTP status code (409 by default)
     * @param \Throwable|null $previous
     */
    public function __construct(
        string    $message = 'Insufficient funds',
        int       $code = 409,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Convert the exception into an HTTP response.
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'errors' => [],
        ], $this->getCode());
    }
}
