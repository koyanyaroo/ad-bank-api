<?php

namespace App\Models;

use App\Enums\EntryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerEntry extends Model
{
    use HasFactory;
    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'transaction_id',
        'account_id',
        'entry_type',
        'amount',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
        'entry_type' => EntryType::class,
    ];

    /**
     * The transaction associated with the ledger entry.
     *
     * @return BelongsTo
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * The account associated with the ledger entry.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
