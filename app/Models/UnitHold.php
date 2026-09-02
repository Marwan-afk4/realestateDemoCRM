<?php

namespace App\Models;

use App\Enums\HoldType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitHold extends Model
{
    protected $fillable = [
        'inventory_unit_id',
        'deal_id',
        'contact_id',
        'type',
        'expires_at',
        'released_at',
        'release_reason',
        'created_by',
    ];

    protected $casts = [
        'type' => HoldType::class,
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function inventoryUnit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->released_at === null && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->released_at === null && $this->expires_at->isPast();
    }
}
