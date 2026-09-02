<?php

namespace App\Services\Crm;

use App\Enums\ActivityType;
use App\Models\Contact;
use App\Models\CrmActivity;
use App\Models\PipelineTicket;
use App\Models\User;

class ActivityLogger
{
    public function log(
        Contact $contact,
        ActivityType $type,
        ?string $title = null,
        ?string $body = null,
        array $meta = [],
        ?PipelineTicket $ticket = null,
        ?User $user = null,
    ): CrmActivity {
        $activity = CrmActivity::create([
            'contact_id' => $contact->id,
            'pipeline_ticket_id' => $ticket?->id,
            'user_id' => $user?->id ?? auth()->id(),
            'type' => $type,
            'title' => $title ?: $type->label(),
            'body' => $body,
            'meta' => $meta ?: null,
        ]);

        if (in_array($type, [ActivityType::Call, ActivityType::Whatsapp, ActivityType::Sms, ActivityType::Email, ActivityType::Meeting, ActivityType::SiteVisit], true)) {
            $contact->forceFill(['last_contacted_at' => now()])->saveQuietly();
        }

        return $activity;
    }
}
