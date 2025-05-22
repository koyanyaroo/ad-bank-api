<?php

namespace App\Services;

use App\Enums\EntryType;
use App\Enums\TransactionStatus;
use App\Exceptions\InsufficientFundsException;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{

    /**
     * Transfer money between accounts.
     *
     * @param int $fromAccountId
     * @param int $toAccountId
     * @param float $amount
     * @param string|null $reference
     * @return Transaction
     * @throws ValidationException
     */
    public function transfer(int $fromAccount, int $toAccount, float $amount, ?string $reference = null): Transaction
    {
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Amount must be greater than zero.']);
        }

        return DB::transaction(function () use ($fromAccount, $toAccount, $amount, $reference) {
            // Lock both accounts for update
            $from = Account::lockForUpdate()->where('account_number', $fromAccount)->firstOrFail();
            $to = Account::lockForUpdate()->where('account_number', $toAccount)->firstOrFail();

            if ($from->balance < $amount) {
                throw new InsufficientFundsException('Insufficient funds in the source account.');
            }

            // 1) Create the transaction record
            $txn = Transaction::create([
                'from_account_id' => $from->id,
                'to_account_id' => $to->id,
                'amount' => $amount,
                'status' => TransactionStatus::COMPLETED->value,
                'reference' => $reference,
            ]);

            // 2) Create ledger entries
            $txn->ledgerEntries()->createMany([
                [
                    'account_id' => $from->id,
                    'entry_type' => EntryType::DEBIT->value,
                    'amount' => $amount,
                ],
                [
                    'account_id' => $to->id,
                    'entry_type' => EntryType::CREDIT->value,
                    'amount' => $amount,
                ],
            ]);

            // 3) Update balances
            $from->decrement('balance', $amount);
            $to->increment('balance', $amount);

            return $txn;
        });
    }
}
