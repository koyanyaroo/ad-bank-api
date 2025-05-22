<?php

namespace App\Services;

use App\Enums\EntryType;
use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AccountService
{

    public function getRecipient(int $accountId)
    {
        return Account::where('id', '<>', $accountId) // Exclude the current account
            ->where('account_number', '<>', Account::SYSTEM_ACCOUNT_NUMBER) // Exclude system account
            ->orderBy('account_name')
            ->get();
    }

    public function getAllTransaction(int $accountId)
    {
        return Transaction::where('from_account_id', $accountId)
            ->orWhere('to_account_id', $accountId)
            ->with('ledgerEntries') // Eager load ledger entries
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function deposit(int $accountId, float $amount, ?string $reference = null): Transaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        return DB::transaction(function () use ($accountId, $amount, $reference) {
            $account = Account::findOrFail($accountId);

            // 1) Create the transaction record
            $txn = Transaction::create([
                'from_account_id' => Account::SYSTEM_ACCOUNT_ID,  // system
                'to_account_id' => $account->id,
                'amount' => $amount,
                'status' => TransactionStatus::COMPLETED->value,
                'reference' => $reference,
            ]);

            // 2) Create ledger entries
            $txn->ledgerEntries()->createMany([
                [
                    'account_id' => 1,
                    'entry_type' => EntryType::DEBIT->value,
                    'amount' => $amount,
                ],
                [
                    'account_id' => $account->id,
                    'entry_type' => EntryType::CREDIT->value,
                    'amount' => $amount,
                ],
            ]);

            // 3) Update the balance
            $account->increment('balance', $amount);

            return $txn;
        });
    }

    public function getBalance(int $accountId): float
    {
        return Account::findOrFail($accountId)->balance;
    }
}
