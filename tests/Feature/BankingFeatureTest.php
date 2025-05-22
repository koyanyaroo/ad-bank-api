<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Account;
use App\Enums\EntryType;
use App\Enums\TransactionStatus;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Seed a “system” account with id=0 (so deposits can FK to it)
    DB::table('accounts')->insert([
        'id' => 1,
        'user_id' => User::factory()->create()->id,
        'account_number' => 'SYSTEM',
        'account_name' => 'System Account',
        'balance' => 0.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

test('protected endpoints require authentication', function () {
    $this->getJson('/api/v1/accounts/balance')
        ->assertUnauthorized();

    $this->postJson('/api/v1/accounts/deposit', ['amount' => 10.00])
        ->assertUnauthorized();

    $this->postJson('/api/v1/accounts/transfer', [
        'to_account' => 'NONEXISTENT',
        'amount' => 5.00,
    ])
        ->assertUnauthorized();
});

test('authenticated user can view their balance', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'balance' => 123.45,
    ]);

    $token = $user->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/accounts/balance')
        ->assertOk()
        ->assertJsonPath('data.balance', 123.45);
});

test('user can deposit funds', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create([
        'user_id' => $user->id,
        'balance' => 100.00,
    ]);
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/accounts/deposit', [
            'amount' => 50.10,
            'reference' => 'Paycheck',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.new_balance', 150.10)
        ->assertJsonStructure(['data' => ['transaction_id', 'new_balance']]);

    // DB assertions
    $this->assertDatabaseHas('transactions', [
        'from_account_id' => 1,
        'to_account_id' => $account->id,
        'amount' => 50.10,
        'status' => TransactionStatus::COMPLETED->value,
        'reference' => 'Paycheck',
    ]);
    $this->assertDatabaseHas('ledger_entries', [
        'account_id' => $account->id,
        'entry_type' => EntryType::CREDIT->value,
        'amount' => 50.10,
    ]);
    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'balance' => 150.10,
    ]);
});

test('deposit with non-positive amount returns validation error', function () {
    $user = User::factory()->create();
    Account::factory()->create(['user_id' => $user->id, 'balance' => 100.00]);
    $token = $user->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/accounts/deposit', ['amount' => 0])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['amount']
        ])
        ->assertJsonPath('errors.amount.0', 'The amount field must be at least 0.1.');
});

test('user can transfer funds to another account', function () {
    $alice = User::factory()
        ->has(Account::factory()->state(['balance' => 100.00]), 'accounts')
        ->create();
    $bob = User::factory()
        ->has(Account::factory()->state(['balance' => 20.00]), 'accounts')
        ->create();

    $aliceAcct = $alice->accounts()->first();
    $bobAcct = $bob->accounts()->first();
    $token = $alice->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/accounts/transfer', [
            'to_account' => $bobAcct->account_number,
            'amount' => 75.00,
            'reference' => 'Rent',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.from_balance', '25.00')
        ->assertJsonPath('data.to_balance', '95.00');

    // ledger entries count
    $entries = DB::table('ledger_entries')
        ->where('transaction_id', $response->json('data.transaction_id'))
        ->pluck('entry_type')
        ->all();

    expect($entries)
        ->toHaveCount(2)
        ->toEqualCanonicalizing([
            EntryType::DEBIT->value,
            EntryType::CREDIT->value,
        ]);
});

test('transfer with insufficient funds returns conflict', function () {
    $alice = User::factory()
        ->has(Account::factory()->state(['balance' => 30.00]), 'accounts')
        ->create();
    $bob = User::factory()
        ->has(Account::factory()->state(['balance' => 50.00]), 'accounts')
        ->create();

    $aliceAcct = $alice->accounts()->first();
    $token = $alice->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/accounts/transfer', [
            'to_account' => $bob->accounts()->first()->account_number,
            'amount' => 100.00,
        ])
        ->assertStatus(409)
        ->assertJsonPath('message', 'Insufficient funds in the source account.');
});
