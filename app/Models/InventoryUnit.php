<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryUnit extends Model
{
    protected $fillable = [
        'uptown_id',
        'developer_id',
        'compound_id',
        'phase',
        'building',
        'floor',
        'unit_number',
        'code',
        'status',
        'list_price',
        'current_price',
        'reserved_until',
        'active_deal_id',
        'active_hold_id',
    ];

    protected $casts = [
        'status' => InventoryStatus::class,
        'list_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'reserved_until' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $unit) {
            if (! $unit->code) {
                $unit->code = self::makeCode($unit);
            }
        });
    }

    public static function makeCode(self $unit): string
    {
        return collect([
            $unit->compound_id ?: 'X',
            $unit->phase ?: '-',
            $unit->building ?: '-',
            $unit->floor ?: '-',
            $unit->unit_number,
        ])->map(fn ($part) => str_replace(' ', '', (string) $part))->implode('-');
    }

    public function uptown(): BelongsTo
    {
        return $this->belongsTo(Uptown::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function compound(): BelongsTo
    {
        return $this->belongsTo(Compound::class);
    }

    public function activeDeal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'active_deal_id');
    }

    public function activeHold(): BelongsTo
    {
        return $this->belongsTo(UnitHold::class, 'active_hold_id');
    }

    public function holds(): HasMany
    {
        return $this->hasMany(UnitHold::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function pipelineTickets(): HasMany
    {
        return $this->hasMany(PipelineTicket::class);
    }

    public function address(): string
    {
        return collect([
            $this->compound?->compound_name,
            $this->phase,
            $this->building ? __('Bldg').' '.$this->building : null,
            $this->floor ? __('Fl.').' '.$this->floor : null,
            $this->unit_number,
        ])->filter()->implode(' · ');
    }

    public function price(): float
    {
        return (float) ($this->current_price ?: $this->list_price ?: $this->uptown?->strat_price ?: 0);
    }
}
