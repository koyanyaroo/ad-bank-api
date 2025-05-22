<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Public
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->post('logout', [AuthController::class, 'logout']);
        Route::prefix('accounts')->group(function () {
            Route::get('balance', [AccountController::class, 'balance'])->name('accounts.balance');
            Route::get('recipients', [AccountController::class, 'recipients'])->name('accounts.recipients');
            Route::post('deposit', [TransactionController::class, 'deposit'])->name('accounts.deposit');
            Route::post('transfer', [TransactionController::class, 'transfer'])->name('accounts.transfer');
            Route::get('transactions', [TransactionController::class, 'index'])->name('accounts.transactions');
        });
    });
});

