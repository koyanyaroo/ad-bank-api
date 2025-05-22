<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HandlesValidationErrors
{
    use ApiResponse;

    /**
     * Override FormRequest failedValidation to return your standard envelope.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();

        // Use your ApiResponse trait's error() method
        $response = $this->error(
            'Validation errors',
            $errors,
            422
        );

        throw new HttpResponseException($response);
    }
}
