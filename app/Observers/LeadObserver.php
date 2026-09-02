<?php

namespace App\Observers;

use App\Enums\ContactSource;
use App\Models\Lead;
use App\Services\Crm\PipelineService;

class LeadObserver
{
    public function __construct(private PipelineService $pipeline)
    {
    }

    public function created(Lead $lead): void
    {
        $this->pipeline->ingest($lead, [
            'name' => $lead->lead_name,
            'phone' => $lead->lead_phone,
            'source' => $lead->marketing_agency_id ? ContactSource::Agency : ContactSource::Broker,
            'preferred_area' => $lead->interested_place,
            'owner_id' => $lead->brocker?->user_id,
        ], [
            'brocker_id' => $lead->brocker_id,
            'owner_id' => $lead->brocker?->user_id,
            'stage' => \App\Enums\PipelineStage::fromLegacyLeadStatus($lead->status?->value ?? (string) $lead->status),
        ]);
    }
}
