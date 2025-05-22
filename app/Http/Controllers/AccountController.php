<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountDropdownResource;
use App\Services\AccountService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get all accounts except those belonging to $userId.
     *
     *
     */
    public function recipients(AccountService $accountService)
    {
        $userId = auth()->user()->id;
        $recipients = $accountService->getRecipient($userId);

        return $this->success(
            AccountDropdownResource::collection($recipients), // use specific resource for dropdown
            'Accounts retrieved successfully'
        );
    }

    /**
     * @param AccountService $accounts
     * @return JsonResponse
     */
    public function balance(AccountService $accounts): JsonResponse
    {
        $acctId = auth()->user()->accounts()->first()->id;
        $balance = $accounts->getBalance($acctId);

        return $this->success([
            'balance' => $balance,
        ], 'Balance retrieved successfully');
    }
}
