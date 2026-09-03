<?php

namespace App\Services\Crm;

use App\Enums\DealStatuses;
use App\Models\Commission;
use App\Models\Deal;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DealRevenueService
{
    /**
     * Total commission revenue — snapshotted amounts first, inventory-based estimate as fallback.
     */
    public function totalRevenue(?Carbon $start = null, ?Carbon $end = null): float
    {
        $snapshotTotal = (float) Commission::query()
            ->when($start && $end, fn (Builder $q) => $q->whereHas(
                'deal',
                fn (Builder $deal) => $this->applyDealPeriod($deal, $start, $end),
            ))
            ->sum('amount');

        $fallbackTotal = Deal::query()
            ->where('status', DealStatuses::Approved)
            ->whereDoesntHave('commission')
            ->when($start && $end, fn (Builder $q) => $this->applyDealPeriod($q, $start, $end))
            ->with(['inventoryUnit.uptown', 'compound', 'brocker', 'activeOffer'])
            ->get()
            ->sum(fn (Deal $deal) => $this->estimateForDeal($deal));

        return round($snapshotTotal + $fallbackTotal, 2);
    }

    public function revenueForDeal(Deal $deal): float
    {
        $deal->loadMissing(['commission', 'inventoryUnit.uptown', 'compound', 'brocker', 'activeOffer']);

        if ($deal->commission) {
            return (float) $deal->commission->amount;
        }

        return $this->estimateForDeal($deal);
    }

    public function estimateForDeal(Deal $deal): float
    {
        if ($deal->commission) {
            return (float) $deal->commission->amount;
        }

        $deal->loadMissing(['inventoryUnit.uptown', 'compound', 'brocker', 'activeOffer']);

        if ($deal->status !== DealStatuses::Approved) {
            return 0.0;
        }

        $unit = $deal->inventoryUnit;
        $closedPrice = (float) ($deal->activeOffer?->net_price ?? $deal->value ?? $unit?->price() ?? 0);

        if ($closedPrice <= 0) {
            return 0.0;
        }

        $percentage = (float) ($deal->brocker?->comission_percentage
            ?: $deal->compound?->commission_percentage
            ?: 0);

        return round($closedPrice * $percentage / 100, 2);
    }

    private function applyDealPeriod(Builder $query, Carbon $start, Carbon $end): void
    {
        $query->where(function (Builder $inner) use ($start, $end) {
            $inner->whereBetween('close_date', [$start->toDateString(), $end->toDateString()])
                ->orWhere(function (Builder $fallback) use ($start, $end) {
                    $fallback->whereNull('close_date')
                        ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);
                });
        });
    }
}
