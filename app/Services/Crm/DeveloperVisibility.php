<?php

namespace App\Services\Crm;

use App\Models\Brocker;
use App\Models\Deal;
use App\Models\Developer;
use App\Models\InventoryUnit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DeveloperVisibility
{
    public function developerId(?User $user): ?int
    {
        if (! $user) {
            return null;
        }

        if ($user->role === 'admin' && request()->filled('developer_id')) {
            return (int) request('developer_id');
        }

        return $user->developer_id;
    }

    public function scopeInventory(Builder $query, User $user): Builder
    {
        $developerId = $this->developerId($user);
        if (! $developerId) {
            return $query;
        }

        return $query->where('developer_id', $developerId);
    }

    public function scopeDeals(Builder $query, User $user): Builder
    {
        $developerId = $this->developerId($user);
        if (! $developerId) {
            return $query;
        }

        return $query->where('developer_id', $developerId);
    }

    public function authorizedBrokerIds(Developer $developer): array
    {
        return $developer->authorizedBrokers()->pluck('brockers.id')->all();
    }

    public function syncAuthorizedBrokers(Developer $developer, array $brokerIds): void
    {
        $developer->authorizedBrokers()->sync($brokerIds);
    }
}
