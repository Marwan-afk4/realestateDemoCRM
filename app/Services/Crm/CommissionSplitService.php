<?php

namespace App\Services\Crm;

use App\Enums\CommissionSplitRole;
use App\Models\Brocker;
use App\Models\Deal;
use App\Models\PipelineTicket;
use App\Models\User;

class CommissionSplitService
{
    /**
     * @return array<int, array{user_id: int, role: CommissionSplitRole, percentage: float, amount: float}>
     */
    public function buildForDeal(Deal $deal, float $commissionAmount): array
    {
        if (! $deal->relationLoaded('brocker')) {
            $deal->load('brocker.user', 'brocker.teamLead');
        }

        if ($deal->lister_broker_id && ! $deal->relationLoaded('listerBroker')) {
            $deal->load('listerBroker.user');
        }

        $closerBroker = $deal->relationLoaded('brocker') ? $deal->getRelation('brocker') : $deal->brocker;
        $closerUserId = $closerBroker?->user_id;
        $manager = $this->managerUser($closerBroker);
        $listerBroker = $this->resolveListerBroker($deal);
        $listerUserId = $listerBroker?->user_id;

        $listerIsDifferent = $listerBroker
            && $closerBroker
            && $listerBroker->id !== $closerBroker->id;

        if ($listerIsDifferent) {
            return $this->splitsWithLister($listerUserId, $closerUserId, $manager?->id, $commissionAmount);
        }

        if ($manager) {
            $cfg = config('crm.commission_splits.without_lister');

            return $this->pack([
                [$closerUserId, CommissionSplitRole::Closer, (float) $cfg['closer']],
                [$manager->id, CommissionSplitRole::Manager, (float) $cfg['manager']],
            ], $commissionAmount);
        }

        return $this->pack([
            [$closerUserId, CommissionSplitRole::Closer, 100.0],
        ], $commissionAmount);
    }

    public function resolveListerBroker(Deal $deal): ?Brocker
    {
        if ($deal->lister_broker_id) {
            if ($deal->relationLoaded('listerBroker')) {
                return $deal->getRelation('listerBroker');
            }

            return $deal->listerBroker ?? Brocker::query()->find($deal->lister_broker_id);
        }

        if (! $deal->brocker_id) {
            return null;
        }

        if ($deal->inventory_unit_id) {
            $ticketBrokerId = PipelineTicket::query()
                ->where('inventory_unit_id', $deal->inventory_unit_id)
                ->whereNotNull('brocker_id')
                ->where('brocker_id', '!=', $deal->brocker_id)
                ->orderBy('id')
                ->value('brocker_id');

            if ($ticketBrokerId) {
                return Brocker::query()->find($ticketBrokerId);
            }
        }

        if ($deal->lead_id) {
            $deal->loadMissing('lead');
            $leadBrokerId = $deal->lead?->brocker_id;
            if ($leadBrokerId && $leadBrokerId !== $deal->brocker_id) {
                return Brocker::query()->find($leadBrokerId);
            }
        }

        return null;
    }

    private function managerUser(?Brocker $broker): ?User
    {
        if (! $broker) {
            return null;
        }

        if ($broker->relationLoaded('teamLead')) {
            return $broker->getRelation('teamLead');
        }

        return $broker->teamLead;
    }

    /**
     * @return array<int, array{user_id: int, role: CommissionSplitRole, percentage: float, amount: float}>
     */
    private function splitsWithLister(?int $listerUserId, ?int $closerUserId, ?int $managerUserId, float $total): array
    {
        $cfg = config('crm.commission_splits.with_lister');
        $listerPct = (float) $cfg['lister'];
        $closerPct = (float) $cfg['closer'];
        $managerPct = $managerUserId ? (float) $cfg['manager'] : 0.0;

        if (! $managerUserId) {
            $closerPct += (float) $cfg['manager'];
        }

        return $this->pack([
            [$listerUserId, CommissionSplitRole::Lister, $listerPct],
            [$closerUserId, CommissionSplitRole::Closer, $closerPct],
            [$managerUserId, CommissionSplitRole::Manager, $managerPct],
        ], $total);
    }

    /**
     * @param  array<int, array{0: ?int, 1: CommissionSplitRole, 2: float}>  $rows
     * @return array<int, array{user_id: int, role: CommissionSplitRole, percentage: float, amount: float}>
     */
    private function pack(array $rows, float $total): array
    {
        $splits = [];

        foreach ($rows as [$userId, $role, $percent]) {
            if (! $userId || $percent <= 0) {
                continue;
            }

            $splits[] = [
                'user_id' => $userId,
                'role' => $role,
                'percentage' => $percent,
                'amount' => round($total * $percent / 100, 2),
            ];
        }

        return $splits;
    }
}
