<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Traits\HandlesValidationErrors;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    use HandlesValidationErrors;

    /**
     * Everyone can hit this request—fine, we’ll gate the real check in the rules.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'to_account' => [
                'required',
                'exists:accounts,account_number',
                function ($attribute, $value, $fail) {
                    // look up the target account
                    $account = Account::where('account_number', $value)->first();
                    if (! $account) {
                        // let the exists: rule handle “not found”
                        return;
                    }

                    // 1) block transfers to the system account
                    if ($account->id === Account::SYSTEM_ACCOUNT_ID) {
                        return $fail('Transfers to the system account are not allowed.');
                    }

                    // 2) block transfers to yourself
                    //    assumes your Account model has a user_id FK
                    if ($account->user_id === $this->user()->id) {
                        return $fail('You cannot transfer to your own account.');
                    }
                },
            ],
            'amount'    => 'required|numeric|min:0.1',
            'reference' => 'nullable|string',
        ];
    }
}
