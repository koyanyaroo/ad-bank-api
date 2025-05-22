<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Account;

class UserAccountSeeder extends Seeder
{
    public function run()
    {
        // Create 10 users, each with 1 account seeded via the AccountFactory
        User::factory(10)
            ->has(
                Account::factory()
                    ->state(function () {
                        // random starting balance between 100 and 1000
                        return ['balance' => 0];
                    })
                    ->count(1),
                'accounts'
            )
            ->create();
    }
}
