<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerReceipt extends Model
{
    protected $fillable = [
        'buyer_installment_id',
        'amount',
        'received_at',
        'reference',
        'path',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    public function installment(): BelongsTo
    {
        return $this->belongsTo(BuyerInstallment::class, 'buyer_installment_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
