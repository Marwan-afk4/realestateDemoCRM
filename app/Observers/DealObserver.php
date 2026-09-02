<?php

namespace App\Observers;

use App\Enums\ContactSource;
use App\Models\Deal;
use App\Models\PipelineTicket;
use App\Services\Crm\ContactResolver;

class DealObserver
{
    public function __construct(private ContactResolver $contacts)
    {
    }

    public function created(Deal $deal): void
    {
        $this->attach($deal);
    }

    public function updating(Deal $deal): void
    {
        if ($deal->isDirty(['fullname', 'phone', 'email', 'lead_id']) && ! $deal->contact_id) {
            $this->attach($deal);
        }
    }

    private function attach(Deal $deal): void
    {
        $contact = $this->contacts->findOrCreate([
            'name' => $deal->fullname,
            'phone' => $deal->phone,
            'email' => $deal->email,
            'national_id' => $deal->nationality_id,
            'source' => ContactSource::Other,
            'uptown_type_id' => $deal->uptown_type_id,
        ]);

        $ticketId = $deal->pipeline_ticket_id;
        if (! $ticketId && $deal->lead_id) {
            $ticketId = PipelineTicket::query()
                ->where('ticketable_type', \App\Models\Lead::class)
                ->where('ticketable_id', $deal->lead_id)
                ->value('id');
        }

        $deal->forceFill([
            'contact_id' => $deal->contact_id ?: $contact->id,
            'pipeline_ticket_id' => $ticketId,
        ])->saveQuietly();
    }
}
