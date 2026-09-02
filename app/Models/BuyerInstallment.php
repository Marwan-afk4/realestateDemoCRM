<?php

namespace App\Models;

use App\Enums\BuyerInstallmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyerInstallment extends Model
{
    protected $fillable = [
        'buyer_payment_plan_id',
        'sequence',
        'label',
        'due_date',
        'amount',
        'paid_amount',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'status' => BuyerInstallmentStatus::class,
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'sequence' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(BuyerPaymentPlan::class, 'buyer_payment_plan_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(BuyerReceipt::class);
    }

    public function remaining(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    public function refreshStatus(): void
    {
        $overdue = $this->due_date->lt(now()->startOfDay());

        if ((float) $this->paid_amount >= (float) $this->amount) {
            $this->status = BuyerInstallmentStatus::Paid;
            $this->paid_at = $this->paid_at ?: now();
        } elseif ((float) $this->paid_amount > 0) {
            $this->status = $overdue
                ? BuyerInstallmentStatus::Overdue
                : BuyerInstallmentStatus::Partial;
        } elseif ($overdue) {
            $this->status = BuyerInstallmentStatus::Overdue;
        } else {
            $this->status = BuyerInstallmentStatus::Pending;
            $this->paid_at = null;
        }
        $this->save();
    }
}
