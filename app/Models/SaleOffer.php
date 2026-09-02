<?php

namespace App\Models;

use App\Enums\SaleOfferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleOffer extends Model
{
    protected $fillable = [
        'deal_id',
        'inventory_unit_id',
        'contact_id',
        'brocker_id',
        'list_price',
        'discount',
        'net_price',
        'payment_method',
        'down_payment_percent',
        'installment_count',
        'valid_until',
        'status',
        'accepted_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'status' => SaleOfferStatus::class,
        'list_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'net_price' => 'decimal:2',
        'valid_until' => 'datetime',
        'accepted_at' => 'datetime',
        'down_payment_percent' => 'integer',
        'installment_count' => 'integer',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function inventoryUnit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function brocker(): BelongsTo
    {
        return $this->belongsTo(Brocker::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SaleDocument::class);
    }

    public static function paymentMethods(): array
    {
        return [
            'cash' => __('Cash'),
            'installment' => __('Installment'),
            'mixed' => __('Mixed'),
        ];
    }
}
