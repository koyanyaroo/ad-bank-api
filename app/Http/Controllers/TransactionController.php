<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\TransactionService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/v1/transactions
     * Paginated list of all transactions involving the user’s primary account.
     */
    public function index(Request $req, AccountService $accounts): JsonResponse
    {
        $account = $req->user()->accounts()->first();
        if (!$account) {
            return $this->error('No account found for this user', 404);
        }

        $transactions = $accounts->getAllTransaction($account->id);

        return $this->success(TransactionResource::collection($transactions), 'Transactions retrieved successfully');
    }

    /**
     * Handle a deposit request.
     *
     * @param DepositRequest $req
     * @param AccountService $accounts
     * @return JsonResponse
     */
    public function deposit(DepositRequest $req, AccountService $accounts): JsonResponse
    {
        $acctId = $req->user()->accounts()->first()->id;

        $txn = $accounts->deposit($acctId, $req->amount, $req->reference);

        return $this->success(
            [
                'transaction_id' => $txn->id,
                'new_balance' => $accounts->getBalance($acctId),
            ],
            'Deposit successful'
        );
    }

    /**
     * Handle a transfer request.
     *
     * @param TransferRequest $req
     * @param TransactionService $txns
     * @return JsonResponse
     * @throws ValidationException
     */
    public function transfer(TransferRequest $req, TransactionService $txns): JsonResponse
    {
        $fromAccount = $req->user()->accounts()->first()->account_number;
        $toAccount = $req->to_account;
        $txn = $txns->transfer($fromAccount, $toAccount, $req->amount, $req->reference);

        return $this->success(
            [
                'transaction_id' => $txn->id,
                'from_balance' => $req->user()->accounts()->first()->balance,
                'to_balance' => $txn->toAccount->balance,
            ],
            'Transfer successful'
        );
    }

}
