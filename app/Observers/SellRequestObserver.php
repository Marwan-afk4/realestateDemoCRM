<?php

namespace App\Observers;

use App\Enums\ContactSource;
use App\Enums\PipelineStage;
use App\Models\SellRequest;
use App\Services\Crm\PipelineService;

class SellRequestObserver
{
    public function __construct(private PipelineService $pipeline)
    {
    }

    public function created(SellRequest $sellRequest): void
    {
        $user = $sellRequest->user;
        $stage = match ($sellRequest->status) {
            'contacted' => PipelineStage::Contacted,
            'approved' => PipelineStage::Won,
            'rejected' => PipelineStage::Lost,
            default => PipelineStage::New,
        };

        $this->pipeline->ingest($sellRequest, [
            'name' => $user?->full_name ?: __('Seller'),
            'phone' => $user?->phone,
            'email' => $user?->email,
            'source' => ContactSource::SellRequest,
            'preferred_area' => $sellRequest->area ?? $sellRequest->city,
            'uptown_type_id' => $sellRequest->uptown_type_id,
            'intent' => 'sell',
        ], [
            'stage' => $stage,
        ]);
    }
}
