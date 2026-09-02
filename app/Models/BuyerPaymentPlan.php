<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyerPaymentPlan extends Model
{
    protected $fillable = [
        'deal_id',
        'contact_id',
        'inventory_unit_id',
        'sale_offer_id',
        'total_price',
        'down_payment',
        'currency',
        'start_date',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'start_date' => 'date',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function inventoryUnit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(SaleOffer::class, 'sale_offer_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(BuyerInstallment::class)->orderBy('sequence');
    }

    public function paidTotal(): float
    {
        return (float) $this->installments->sum('paid_amount');
    }

    public function remaining(): float
    {
        return max(0, (float) $this->total_price - $this->paidTotal());
    }

    public function overdueCount(): int
    {
        return $this->installments->where('status', \App\Enums\BuyerInstallmentStatus::Overdue)->count();
    }
}
