<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;
    public const SYSTEM_ACCOUNT_ID = 1;
    public const SYSTEM_ACCOUNT_NAME = 'System Account';
    public const SYSTEM_ACCOUNT_NUMBER = '000000';

    protected $fillable = [
        'user_id',
        'account_number',
        'account_name',
        'balance',
    ];

    protected $casts = [
        'balance'    => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionsFrom(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    public function transactionsTo(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
