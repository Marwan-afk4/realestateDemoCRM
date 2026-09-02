<?php

namespace App\Models;

use App\Enums\SaleDocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDocument extends Model
{
    protected $fillable = [
        'deal_id',
        'sale_offer_id',
        'inventory_unit_id',
        'contact_id',
        'brocker_id',
        'type',
        'title',
        'body',
        'issued_at',
        'signed_at',
        'issued_by',
    ];

    protected $casts = [
        'type' => SaleDocumentType::class,
        'issued_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(SaleOffer::class, 'sale_offer_id');
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

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
