<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a standardized success JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(array|object|null $data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a standardized error JSON response.
     *
     * @param string $message
     * @param array|string $errors
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = 'Something went wrong.', array|string $errors = [], int $status = 400): JsonResponse
    {
        // how to set response http status code in laravel
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
