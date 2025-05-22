<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SystemAccountSeeder extends Seeder
{
    public function run()
    {
        // 1) Make or fetch a dedicated “system” user
        $systemUser = User::firstOrCreate(
            ['email' => 'system@bank.local'],
            [
                'name' => 'System User',
                'password' => bcrypt(Str::random(16)),
            ]
        );

        // 2) Insert the system account with id = 0
        DB::table('accounts')->updateOrInsert(
            ['id' => Account::SYSTEM_ACCOUNT_ID],
            [
                'user_id' => $systemUser->id,
                'account_number' => Account::SYSTEM_ACCOUNT_NUMBER,
                'account_name' => Account::SYSTEM_ACCOUNT_NAME,
                'balance' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
