<?php

namespace App\Observers;

use App\Enums\ContactSource;
use App\Enums\PipelineStage;
use App\Models\Brocker;
use App\Models\BuyAppartmentInstallment;
use App\Models\PipelineTicket;
use App\Services\Crm\PipelineService;

class MortgageRequestObserver
{
    public function __construct(private PipelineService $pipeline)
    {
    }

    public function created(BuyAppartmentInstallment $request): void
    {
        $this->pipeline->ingest($request, [
            'name' => $request->user?->full_name ?: __('Buyer'),
            'phone' => $request->user?->phone,
            'email' => $request->user?->email,
            'source' => ContactSource::Mortgage,
            'preferred_area' => $request->area ?? $request->city,
            'intent' => 'buy',
            'payment_preference' => 'installment',
            'owner_id' => config('crm.default_inbound_owner_user_id'),
        ], $this->ticketAttributes($request));
    }

    public function updated(BuyAppartmentInstallment $request): void
    {
        if (! $request->wasChanged('status')) {
            return;
        }

        $ticket = $this->findTicket($request);
        if (! $ticket) {
            return;
        }

        $stage = $this->stageForStatus($request->status);
        if ($ticket->stage !== $stage) {
            $this->pipeline->changeStage($ticket, $stage);
        }
    }

    private function ticketAttributes(BuyAppartmentInstallment $request): array
    {
        $ownerId = config('crm.default_inbound_owner_user_id');
        $brockerId = $ownerId
            ? Brocker::query()->where('user_id', $ownerId)->value('id')
            : null;

        return [
            'stage' => $this->stageForStatus($request->status),
            'owner_id' => $ownerId,
            'brocker_id' => $brockerId,
        ];
    }

    private function stageForStatus(?string $status): PipelineStage
    {
        return match ($status) {
            'contacted' => PipelineStage::Contacted,
            'approved' => PipelineStage::Won,
            'rejected' => PipelineStage::Lost,
            default => PipelineStage::New,
        };
    }

    private function findTicket(BuyAppartmentInstallment $request): ?PipelineTicket
    {
        return PipelineTicket::query()
            ->where('ticketable_type', $request->getMorphClass())
            ->where('ticketable_id', $request->id)
            ->first();
    }
}
