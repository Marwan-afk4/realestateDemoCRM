<?php

namespace App\Services\Crm;

use App\Enums\InventoryStatus;
use App\Models\Contact;
use App\Models\InventoryUnit;
use Illuminate\Support\Collection;

class UnitMatchingService
{
    public function matchForContact(Contact $contact, int $limit = 25): Collection
    {
        $query = InventoryUnit::query()
            ->with(['compound', 'uptown.uptownType'])
            ->whereIn('status', [
                InventoryStatus::Available,
                InventoryStatus::Held,
            ]);

        if ($contact->budget_max) {
            $max = (float) $contact->budget_max;
            $query->where(function ($inner) use ($max) {
                $inner->where('current_price', '<=', $max)
                    ->orWhere('list_price', '<=', $max)
                    ->orWhere(function ($fallback) use ($max) {
                        $fallback->whereNull('current_price')
                            ->whereNull('list_price')
                            ->whereHas('uptown', fn ($uptown) => $uptown->where('strat_price', '<=', $max));
                    });
            });
        }

        if ($contact->budget_min) {
            $min = (float) $contact->budget_min;
            $query->where(function ($inner) use ($min) {
                $inner->where('current_price', '>=', $min)
                    ->orWhere('list_price', '>=', $min)
                    ->orWhereHas('uptown', fn ($uptown) => $uptown->where('strat_price', '>=', $min));
            });
        }

        if ($contact->preferred_area) {
            $area = trim((string) $contact->preferred_area);
            $query->where(function ($inner) use ($area) {
                $inner->whereHas('compound', fn ($compound) => $compound->where('compound_name', 'like', '%'.$area.'%'))
                    ->orWhereHas('uptown', function ($uptown) use ($area) {
                        $uptown->where('name_en', 'like', '%'.$area.'%')
                            ->orWhere('name_ar', 'like', '%'.$area.'%');
                    });
            });
        }

        if ($contact->uptown_type_id) {
            $query->whereHas('uptown', fn ($uptown) => $uptown->where('uptown_type_id', $contact->uptown_type_id));
        }

        return $query
            ->orderByRaw('COALESCE(current_price, list_price, 0) asc')
            ->limit($limit)
            ->get()
            ->map(function (InventoryUnit $unit) use ($contact) {
                return [
                    'unit' => $unit,
                    'price' => $unit->price(),
                    'score' => $this->score($contact, $unit),
                ];
            })
            ->sortByDesc('score')
            ->values();
    }

    private function score(Contact $contact, InventoryUnit $unit): int
    {
        $score = 0;
        $price = $unit->price();

        if ($contact->budget_min && $contact->budget_max && $price >= (float) $contact->budget_min && $price <= (float) $contact->budget_max) {
            $score += 40;
        } elseif ($contact->budget_max && $price <= (float) $contact->budget_max) {
            $score += 25;
        }

        if ($contact->uptown_type_id && (int) $unit->uptown?->uptown_type_id === (int) $contact->uptown_type_id) {
            $score += 30;
        }

        if ($contact->preferred_area) {
            $area = mb_strtolower((string) $contact->preferred_area);
            $compound = mb_strtolower((string) $unit->compound?->compound_name);
            if ($compound && str_contains($compound, $area)) {
                $score += 20;
            }
        }

        if ($unit->status === InventoryStatus::Available) {
            $score += 10;
        }

        return $score;
    }
}
