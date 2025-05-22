<?php

use App\Exceptions\InsufficientFundsException;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Enums\EntryType;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Alice with $100, Bob with $20
    $this->alice = User::factory()
        ->has(Account::factory()->state(['balance' => 100.00]), 'accounts')
        ->create();

    $this->bob = User::factory()
        ->has(Account::factory()->state(['balance' => 20.00]), 'accounts')
        ->create();

    $this->aliceAcct = $this->alice->accounts()->first();
    $this->bobAcct   = $this->bob->accounts()->first();
    $this->service   = new TransactionService();
});

it('transfers funds correctly between accounts', function () {
    $txn = $this->service->transfer(
        $this->aliceAcct->account_number,
        $this->bobAcct->account_number,
        50.00,
        'Rent'
    );

    // persisted transaction
    expect($txn)
        ->toBeInstanceOf(Transaction::class)
        ->and($this->aliceAcct->fresh()->balance)->toBe('50.00')
        ->and($this->bobAcct->fresh()->balance)->toBe('70.00');

    // ledger entries
    $entries = $txn->ledgerEntries;
    expect($entries)->toHaveCount(2)
        ->and($entries->where('entry_type', EntryType::DEBIT))->not()->toBeEmpty()
        ->and($entries->where('entry_type', EntryType::CREDIT))->not()->toBeEmpty();
});

it('throws validation exception when insufficient funds', function () {
    $this->service->transfer(
        $this->aliceAcct->account_number,
        $this->bobAcct->account_number,
        200.00,
        null
    );
})->throws(InsufficientFundsException::class, 'Insufficient funds in the source account.');
