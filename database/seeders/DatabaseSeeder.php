<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) System account must exist before any user transactions
        $this->call(SystemAccountSeeder::class);

        // 2) Regular users & accounts
        $this->call(UserAccountSeeder::class);
    }
}
