<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationErrors;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    use HandlesValidationErrors;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * assumption: the minimal deposit amount is 0.1
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.1', 'reference' => 'nullable|string'
        ];
    }
}
