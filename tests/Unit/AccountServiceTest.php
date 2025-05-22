<?php

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Enums\EntryType;
use App\Enums\TransactionStatus;
use App\Services\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Unit');

beforeEach(function () {
    // Create a user with one account at $100 balance
    $this->user = User::factory()
        ->has(Account::factory()->state(['balance' => 100.00]), 'accounts')
        ->create();

    $this->account = $this->user->accounts()->first();

    DB::table('accounts')->insert([
        'id'             => 0,
        'user_id'        => $this->user->id,         // satisfy FK to users.id
        'account_number' => 'SYSTEM',
        'account_name'   => 'System Account',
        'balance'        => 0.00,
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);

    $this->service = new AccountService();
});

it('deposits funds and creates correct transaction & ledgers', function () {
    $txn = $this->service->deposit(
        $this->account->id,
        50.00,
        'Test deposit'
    );

    // persisted transaction
    expect($txn)
        ->toBeInstanceOf(Transaction::class)
        ->and((float) $txn->amount)->toBe(50.00)
        ->and($txn->status->value)->toBe(TransactionStatus::COMPLETED->value)
        ->and($txn->reference)->toBe('Test deposit')
        ->and((float) $this->account->fresh()->balance)->toBe(150.00); // balance updated

    // 1) Grab the string values of each entry_type
    $types = $txn
        ->ledgerEntries
        ->pluck('entry_type.value')
        ->all();

    // 2) Assert you got both values, order-agnostic
    expect($types)
        ->toHaveCount(2)
        ->toEqualCanonicalizing([
            EntryType::DEBIT->value,
            EntryType::CREDIT->value,
        ]);
});

it('rejects deposits with non-positive amounts', function () {
    $this->service->deposit($this->account->id, 0.00, null);
})->throws(InvalidArgumentException::class, 'Amount must be greater than zero.');
