<?php

namespace App\Services\Crm;

use App\Models\Contact;
use App\Models\InventoryUnit;
use App\Models\Lead;
use App\Models\MarketingAgency;
use App\Models\PipelineTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AgencyVisibility
{
    public function agencyId(?User $user): ?int
    {
        if (! $user) {
            return null;
        }

        if ($user->role === 'admin' && request()->filled('agency_id')) {
            return (int) request('agency_id');
        }

        return $user->marketing_agency_id;
    }

    public function scopeLeads(Builder $query, User $user): Builder
    {
        $agencyId = $this->agencyId($user);
        if (! $agencyId || $user->can('view-all-pipeline')) {
            return $query;
        }

        return $query->where('marketing_agency_id', $agencyId);
    }

    public function scopeAgencyUsers(Builder $query, User $user): Builder
    {
        $agencyId = $this->agencyId($user);
        if (! $agencyId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('marketing_agency_id', $agencyId)->where('role', 'agency');
    }
}
