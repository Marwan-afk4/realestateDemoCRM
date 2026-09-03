<?php

namespace App\Observers;

use App\Enums\ContactSource;
use App\Enums\PipelineStage;
use App\Models\Brocker;
use App\Models\PipelineTicket;
use App\Models\SellRequest;
use App\Services\Crm\PipelineService;

class SellRequestObserver
{
    public function __construct(private PipelineService $pipeline)
    {
    }

    public function created(SellRequest $sellRequest): void
    {
        $this->pipeline->ingest($sellRequest, [
            'name' => $sellRequest->user?->full_name ?: __('Seller'),
            'phone' => $sellRequest->user?->phone,
            'email' => $sellRequest->user?->email,
            'source' => ContactSource::SellRequest,
            'preferred_area' => $sellRequest->area ?? $sellRequest->city,
            'uptown_type_id' => $sellRequest->uptown_type_id,
            'intent' => 'sell',
            'owner_id' => config('crm.default_inbound_owner_user_id'),
        ], $this->ticketAttributes($sellRequest));
    }

    public function updated(SellRequest $sellRequest): void
    {
        if (! $sellRequest->wasChanged('status')) {
            return;
        }

        $ticket = $this->findTicket($sellRequest);
        if (! $ticket) {
            return;
        }

        $stage = $this->stageForStatus($sellRequest->status);
        if ($ticket->stage !== $stage) {
            $this->pipeline->changeStage($ticket, $stage);
        }
    }

    private function ticketAttributes(SellRequest $sellRequest): array
    {
        $ownerId = config('crm.default_inbound_owner_user_id');
        $brockerId = $ownerId
            ? Brocker::query()->where('user_id', $ownerId)->value('id')
            : null;

        return [
            'stage' => $this->stageForStatus($sellRequest->status),
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

    private function findTicket(SellRequest $sellRequest): ?PipelineTicket
    {
        return PipelineTicket::query()
            ->where('ticketable_type', $sellRequest->getMorphClass())
            ->where('ticketable_id', $sellRequest->id)
            ->first();
    }
}
