<?php

namespace App\Services\Crm;

use App\Models\Brocker;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SalesVisibility
{
    public function appliesTo(?User $user): bool
    {
        return $user !== null;
    }

    public function canViewAll(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->can('view-all-pipeline');
    }

    public function canViewTeam(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->can('view-team-pipeline') || $this->teamMemberUserIds($user) !== [$user->id];
    }

    /**
     * @return list<int>
     */
    public function visibleUserIds(User $user): array
    {
        if ($this->canViewAll($user)) {
            return [];
        }

        $ids = [$user->id];

        if ($user->can('view-team-pipeline')) {
            $ids = array_merge($ids, $this->teamMemberUserIds($user));
        }

        return array_values(array_unique($ids));
    }

    public function scopeTickets(Builder $query, User $user): Builder
    {
        if ($this->canViewAll($user)) {
            return $query;
        }

        $ids = $this->visibleUserIds($user);

        return $query->where(function (Builder $inner) use ($ids, $user) {
            $inner->whereIn('owner_id', $ids)
                ->orWhereHas('brocker', fn (Builder $broker) => $broker->whereIn('user_id', $ids));
        });
    }

    public function scopeContacts(Builder $query, User $user): Builder
    {
        if ($this->canViewAll($user)) {
            return $query;
        }

        $ids = $this->visibleUserIds($user);

        return $query->where(function (Builder $inner) use ($ids) {
            $inner->whereIn('owner_id', $ids)
                ->orWhereHas('tickets', fn (Builder $tickets) => $tickets->whereIn('owner_id', $ids));
        });
    }

    public function scopeTasks(Builder $query, User $user): Builder
    {
        if ($this->canViewAll($user)) {
            return $query;
        }

        return $query->whereIn('owner_id', $this->visibleUserIds($user));
    }

    public function scopeDeals(Builder $query, User $user): Builder
    {
        if ($this->canViewAll($user)) {
            return $query;
        }

        $ids = $this->visibleUserIds($user);

        return $query->where(function (Builder $inner) use ($ids) {
            $inner->whereHas('brocker', fn (Builder $broker) => $broker->whereIn('user_id', $ids))
                ->orWhereHas('ticket', fn (Builder $ticket) => $ticket->whereIn('owner_id', $ids));
        });
    }

    /**
     * @return list<int>
     */
    public function teamMemberUserIds(User $user): array
    {
        $ids = Brocker::query()
            ->where('team_lead_id', $user->id)
            ->pluck('user_id')
            ->all();

        $ids[] = $user->id;

        return array_values(array_unique(array_map('intval', $ids)));
    }

    public function ownsTicket(User $user, $ticket): bool
    {
        if ($this->canViewAll($user)) {
            return true;
        }

        $ids = $this->visibleUserIds($user);

        return in_array((int) $ticket->owner_id, $ids, true)
            || ($ticket->brocker && in_array((int) $ticket->brocker->user_id, $ids, true));
    }
}
