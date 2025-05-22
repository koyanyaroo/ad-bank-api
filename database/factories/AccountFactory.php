<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory’s corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model’s default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // Associate with a new User by default; you can override in tests
            'user_id' => User::factory(),
            // Use Faker’s bankAccountNumber for uniqueness
            'account_number' => $this->faker->unique()->randomNumber(6),
            'account_name' => $this->faker->randomElement(['Checking', 'Savings']) . ' Account',
            // Random balance between 0 and 10,000
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
