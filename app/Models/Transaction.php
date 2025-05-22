<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Transaction model representing a financial transaction between accounts.
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount',
        'status',
        'reference',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => TransactionStatus::class,
    ];


    /**
     * The account that initiated the transaction.
     *
     * @return BelongsTo
     */
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    /**
     * The account that received the transaction.
     *
     * @return BelongsTo
     */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    /**
     * The ledger entries associated with the transaction.
     *
     * @return HasMany
     */
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}
