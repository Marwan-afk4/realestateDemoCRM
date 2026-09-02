<?php

namespace App\Services\Crm;

use App\Enums\ActivityType;
use App\Enums\DealStatuses;
use App\Enums\HoldType;
use App\Enums\InventoryStatus;
use App\Models\Deal;
use App\Models\InventoryUnit;
use App\Models\UnitHold;
use App\Models\Uptown;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function __construct(private ActivityLogger $activities)
    {
    }

    public function ensureForUptown(Uptown $uptown): InventoryUnit
    {
        $existing = InventoryUnit::query()->where('uptown_id', $uptown->id)->first();
        if ($existing) {
            return $existing;
        }

        return InventoryUnit::create([
            'uptown_id' => $uptown->id,
            'developer_id' => $uptown->developer_id,
            'compound_id' => $uptown->compound_id,
            'phase' => 'listing',
            'building' => (string) $uptown->id,
            'floor' => '',
            'unit_number' => $uptown->code ?: ('U-'.$uptown->id),
            'status' => InventoryStatus::fromUptownStatus($uptown->status),
            'list_price' => $uptown->strat_price,
            'current_price' => $uptown->strat_price,
            'active_deal_id' => $uptown->reserved_deal_id,
        ]);
    }

    public function resolveForDeal(Deal $deal): InventoryUnit
    {
        if ($deal->inventory_unit_id) {
            return InventoryUnit::query()->lockForUpdate()->findOrFail($deal->inventory_unit_id);
        }

        if ($deal->uptown_id) {
            $uptown = Uptown::query()->lockForUpdate()->findOrFail($deal->uptown_id);
            $unit = $this->ensureForUptown($uptown);
            $deal->forceFill(['inventory_unit_id' => $unit->id])->saveQuietly();

            return InventoryUnit::query()->lockForUpdate()->findOrFail($unit->id);
        }

        throw ValidationException::withMessages([
            'inventory_unit_id' => __('A deal must be attached to a physical unit before it can be held, reserved, or approved.'),
        ]);
    }

    public function placeHold(
        InventoryUnit $unit,
        Deal $deal,
        HoldType $type,
        ?\DateTimeInterface $expiresAt = null,
        ?User $user = null,
    ): UnitHold {
        return DB::transaction(function () use ($unit, $deal, $type, $expiresAt, $user) {
            $unit = InventoryUnit::query()->lockForUpdate()->findOrFail($unit->id);
            $this->assertNotTakenByOther($unit, $deal->id);

            $expiresAt = $expiresAt ?: now()->addHours($type->defaultHours());
            $status = $type->inventoryStatus();

            $this->releaseActiveHold($unit, 'replaced');

            $hold = UnitHold::create([
                'inventory_unit_id' => $unit->id,
                'deal_id' => $deal->id,
                'contact_id' => $deal->contact_id,
                'type' => $type,
                'expires_at' => $expiresAt,
                'created_by' => $user?->id ?? auth()->id(),
            ]);

            $this->writeStatus($unit, $status, $deal, $hold, $expiresAt);
            $deal->forceFill(['inventory_unit_id' => $unit->id])->saveQuietly();

            if ($deal->contact) {
                $this->activities->log(
                    $deal->contact,
                    ActivityType::System,
                    $type->label(),
                    __('Unit :code held until :until.', [
                        'code' => $unit->code,
                        'until' => $expiresAt->format('Y-m-d H:i'),
                    ]),
                    ['inventory_unit_id' => $unit->id, 'hold_id' => $hold->id],
                    $deal->ticket,
                    $user,
                );
            }

            return $hold;
        });
    }

    public function applyDealStatus(Deal $deal, DealStatuses $status): InventoryUnit
    {
        $unit = $this->resolveForDeal($deal);

        return match ($status) {
            DealStatuses::SemiDone => tap($unit, function () use ($unit, $deal) {
                $this->placeHold($unit, $deal, HoldType::Reservation);
            }),
            DealStatuses::Approved => $this->markSold($unit, $deal),
            DealStatuses::Rejected => tap($unit, fn () => $this->release($unit, $deal, 'deal_rejected')),
            default => $unit,
        };
    }

    public function markContracted(InventoryUnit $unit, Deal $deal): InventoryUnit
    {
        return DB::transaction(function () use ($unit, $deal) {
            $unit = InventoryUnit::query()->lockForUpdate()->findOrFail($unit->id);
            $this->assertNotTakenByOther($unit, $deal->id);
            $this->writeStatus($unit, InventoryStatus::Contracted, $deal, $unit->activeHold, $unit->reserved_until);

            return $unit->fresh();
        });
    }

    public function markSold(InventoryUnit $unit, Deal $deal): InventoryUnit
    {
        return DB::transaction(function () use ($unit, $deal) {
            $unit = InventoryUnit::query()->lockForUpdate()->findOrFail($unit->id);
            $this->assertNotTakenByOther($unit, $deal->id);
            $this->writeStatus($unit, InventoryStatus::Sold, $deal, $unit->activeHold, null);

            if (! $deal->value) {
                $deal->value = $unit->price();
                $deal->saveQuietly();
            }

            return $unit->fresh();
        });
    }

    public function markHandedOver(InventoryUnit $unit, Deal $deal): InventoryUnit
    {
        return DB::transaction(function () use ($unit, $deal) {
            $unit = InventoryUnit::query()->lockForUpdate()->findOrFail($unit->id);
            if ($unit->active_deal_id && $unit->active_deal_id !== $deal->id) {
                throw ValidationException::withMessages([
                    'inventory_unit_id' => __('This unit is attached to another deal.'),
                ]);
            }
            if ($unit->status !== InventoryStatus::Sold && $unit->status !== InventoryStatus::HandedOver) {
                throw ValidationException::withMessages([
                    'status' => __('Handover is only available after the unit is sold.'),
                ]);
            }
            $this->writeStatus($unit, InventoryStatus::HandedOver, $deal, $unit->activeHold, null);

            return $unit->fresh();
        });
    }

    public function release(InventoryUnit $unit, ?Deal $deal = null, string $reason = 'released'): void
    {
        DB::transaction(function () use ($unit, $deal, $reason) {
            $unit = InventoryUnit::query()->lockForUpdate()->find($unit->id);
            if (! $unit) {
                return;
            }
            if ($deal && $unit->active_deal_id && $unit->active_deal_id !== $deal->id) {
                return;
            }
            $this->releaseActiveHold($unit, $reason);
            $this->writeStatus($unit, InventoryStatus::Available, null, null, null);
        });
    }

    public function releaseExpired(): int
    {
        $holds = UnitHold::query()
            ->with('inventoryUnit')
            ->whereNull('released_at')
            ->where('expires_at', '<=', now())
            ->get();

        $released = 0;
        foreach ($holds as $hold) {
            $unit = $hold->inventoryUnit;
            if (! $unit) {
                $hold->forceFill(['released_at' => now(), 'release_reason' => 'expired'])->save();
                $released++;
                continue;
            }
            if (in_array($unit->status, [InventoryStatus::Sold, InventoryStatus::HandedOver, InventoryStatus::Contracted], true)) {
                $hold->forceFill(['released_at' => now(), 'release_reason' => 'superseded'])->save();
                continue;
            }
            $this->release($unit, $hold->deal, 'expired');
            $released++;
        }

        return $released;
    }

    private function assertNotTakenByOther(InventoryUnit $unit, int $dealId): void
    {
        if ($unit->active_deal_id && $unit->active_deal_id !== $dealId) {
            throw ValidationException::withMessages([
                'inventory_unit_id' => __('This unit is already held, reserved, or sold.'),
            ]);
        }

        if ($unit->status === InventoryStatus::Sold || $unit->status === InventoryStatus::HandedOver) {
            if ($unit->active_deal_id !== $dealId) {
                throw ValidationException::withMessages([
                    'inventory_unit_id' => __('This unit is already sold.'),
                ]);
            }
        }
    }

    private function releaseActiveHold(InventoryUnit $unit, string $reason): void
    {
        if (! $unit->active_hold_id) {
            UnitHold::query()
                ->where('inventory_unit_id', $unit->id)
                ->whereNull('released_at')
                ->update(['released_at' => now(), 'release_reason' => $reason]);

            return;
        }

        UnitHold::query()->whereKey($unit->active_hold_id)->whereNull('released_at')
            ->update(['released_at' => now(), 'release_reason' => $reason]);
    }

    private function writeStatus(
        InventoryUnit $unit,
        InventoryStatus $status,
        ?Deal $deal,
        ?UnitHold $hold,
        mixed $reservedUntil,
    ): void {
        $unit->fill([
            'status' => $status,
            'active_deal_id' => $deal?->id,
            'active_hold_id' => $hold?->id,
            'reserved_until' => $reservedUntil,
        ])->save();

        if ($unit->uptown_id) {
            Uptown::query()->whereKey($unit->uptown_id)->update([
                'status' => $status->toUptownStatus(),
                'reserved_deal_id' => in_array($status, [InventoryStatus::Available], true) ? null : $deal?->id,
            ]);
        }
    }
}
