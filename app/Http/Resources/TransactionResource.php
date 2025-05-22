<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at->toDateTimeString(),
            'from_account' => $this->fromAccount->account_number ?? null,
            'to_account' => $this->toAccount->account_number ?? null,
            'amount' =>$this->amount,
            'reference' => $this->reference,
            'entry_type' => $this->from_account_id === auth()->user()->accounts()->first()->id ? 'DB' : 'CR',
            'status' => $this->status,
        ];
    }
}
