<?php

namespace Database\Factories;


use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{

    /**
     * The name of the factory’s corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            // Associate with a new Account by default; you can override in tests
            'from_account_id' => 1, // System account
            'to_account_id' => Account::factory(), // New account
            'amount' => $this->faker->randomFloat(0, 100, 1000),
            'transaction_type' => 'credit',
            'status' => 'completed',
            'reference' => $this->faker->shortText(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
