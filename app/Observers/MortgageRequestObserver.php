<?php

namespace App\Observers;

use App\Enums\ContactSource;
use App\Enums\PipelineStage;
use App\Models\BuyAppartmentInstallment;
use App\Services\Crm\PipelineService;

class MortgageRequestObserver
{
    public function __construct(private PipelineService $pipeline)
    {
    }

    public function created(BuyAppartmentInstallment $request): void
    {
        $user = $request->user;
        $stage = match ($request->status) {
            'contacted' => PipelineStage::Contacted,
            'approved' => PipelineStage::Won,
            'rejected' => PipelineStage::Lost,
            default => PipelineStage::New,
        };

        $this->pipeline->ingest($request, [
            'name' => $user?->full_name ?: __('Buyer'),
            'phone' => $user?->phone,
            'email' => $user?->email,
            'source' => ContactSource::Mortgage,
            'preferred_area' => $request->area ?? $request->city,
            'intent' => 'buy',
            'payment_preference' => 'installment',
        ], [
            'stage' => $stage,
        ]);
    }
}
