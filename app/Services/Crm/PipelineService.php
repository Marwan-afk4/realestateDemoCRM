<?php

namespace App\Services\Crm;

use App\Enums\ActivityType;
use App\Enums\CrmTaskType;
use App\Enums\InventoryStatus;
use App\Enums\LeadStatuses;
use App\Enums\LostReason;
use App\Enums\PipelineStage;
use App\Enums\PipelineTicketType;
use App\Models\Brocker;
use App\Models\BuyAppartmentInstallment;
use App\Models\Contact;
use App\Models\CrmTask;
use App\Models\InventoryUnit;
use App\Models\Lead;
use App\Models\PipelineTicket;
use App\Models\SellRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PipelineService
{
    public function __construct(
        private ContactResolver $contacts,
        private ActivityLogger $activities,
    ) {}

    public function openLeadForContact(Contact $contact, ?User $user = null): PipelineTicket
    {
        $existing = $contact->tickets()
            ->where('type', PipelineTicketType::Lead)
            ->whereIn('stage', array_map(fn (PipelineStage $stage) => $stage->value, PipelineStage::open()))
            ->latest('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        $brokerId = $contact->owner_id
            ? Brocker::query()->where('user_id', $contact->owner_id)->value('id')
            : null;

        $lead = Lead::create([
            'lead_name' => $contact->name,
            'lead_phone' => $contact->phone,
            'interested_place' => $contact->preferred_area ?: __('Walk-in'),
            'contact_id' => $contact->id,
            'brocker_id' => $brokerId,
            'status' => LeadStatuses::Pending,
        ]);

        $ticket = $lead->ticket()->first();

        if (! $ticket) {
            $ticket = $this->ingest($lead, [
                'name' => $contact->name,
                'phone' => $contact->phone,
                'email' => $contact->email,
                'source' => $contact->source?->value,
                'preferred_area' => $contact->preferred_area,
                'owner_id' => $contact->owner_id ?? $user?->id,
            ], [
                'brocker_id' => $brokerId,
                'owner_id' => $contact->owner_id ?? $user?->id,
                'stage' => PipelineStage::New,
            ]);
        }

        return $ticket;
    }

    public function ingest(Model $ticketable, array $person, array $ticketAttributes = []): PipelineTicket
    {
        return DB::transaction(function () use ($ticketable, $person, $ticketAttributes) {
            $contact = $this->contacts->findOrCreate($person);

            if ($ticketable->getConnection()->getSchemaBuilder()->hasColumn($ticketable->getTable(), 'contact_id')) {
                $ticketable->contact_id = $contact->id;
                $ticketable->saveQuietly();
            }

            $ownerId = $ticketAttributes['owner_id'] ?? $contact->owner_id;
            $brockerId = $ticketAttributes['brocker_id'] ?? null;
            $stage = $ticketAttributes['stage'] ?? PipelineStage::New;
            if (is_string($stage)) {
                $stage = PipelineStage::tryFrom($stage) ?? PipelineStage::New;
            }

            if (! $ownerId && $brockerId) {
                $ownerId = Brocker::query()->where('id', $brockerId)->value('user_id');
            }

            $ticket = PipelineTicket::query()->firstOrCreate(
                [
                    'ticketable_type' => $ticketable->getMorphClass(),
                    'ticketable_id' => $ticketable->id,
                ],
                [
                    'contact_id' => $contact->id,
                    'owner_id' => $ownerId,
                    'brocker_id' => $brockerId,
                    'type' => PipelineTicketType::fromModel($ticketable),
                    'stage' => $stage,
                    'probability' => $stage->probability(),
                    'locked_at' => $ownerId ? now() : null,
                    'locked_by' => $ownerId,
                    'stage_changed_at' => now(),
                ]
            );

            if ($ticket->wasRecentlyCreated) {
                $this->activities->log(
                    $contact,
                    ActivityType::System,
                    __('Ticket created'),
                    __('A :type ticket was opened.', ['type' => $ticket->type->label()]),
                    ['ticket_id' => $ticket->id],
                    $ticket,
                );

                $this->ensureNextAction($ticket, $ownerId);
            }

            return $ticket->fresh(['contact']);
        });
    }

    public function changeStage(
        PipelineTicket $ticket,
        PipelineStage $stage,
        ?User $user = null,
        ?LostReason $lostReason = null,
        ?string $lostNote = null,
    ): PipelineTicket {
        if ($stage === PipelineStage::Lost && ! $lostReason) {
            throw ValidationException::withMessages([
                'lost_reason' => __('A lost reason is required.'),
            ]);
        }

        $from = $ticket->stage;

        $ticket->fill([
            'stage' => $stage,
            'probability' => $stage->probability(),
            'lost_reason' => $stage === PipelineStage::Lost ? $lostReason : null,
            'lost_note' => $stage === PipelineStage::Lost ? $lostNote : null,
            'stage_changed_at' => now(),
            'stage_changed_by' => $user?->id ?? auth()->id(),
        ])->save();

        $this->syncLegacyStatus($ticket, $stage);

        $body = __('Moved from :from to :to.', [
            'from' => $from->label(),
            'to' => $stage->label(),
        ]);
        if ($stage === PipelineStage::Lost) {
            $body .= ' '.__('Reason').': '.$lostReason->label();
            if ($lostNote) {
                $body .= ' — '.$lostNote;
            }
        }

        $this->activities->log(
            $ticket->contact,
            ActivityType::StageChange,
            __('Stage changed'),
            $body,
            [
                'from' => $from->value,
                'to' => $stage->value,
                'lost_reason' => $lostReason?->value,
            ],
            $ticket,
            $user,
        );

        return $ticket->fresh();
    }

    public function assign(PipelineTicket $ticket, Brocker $broker, ?User $actor = null, ?\DateTimeInterface $expiresAt = null): PipelineTicket
    {
        if ($ticket->isLocked() && $ticket->brocker_id && $ticket->brocker_id !== $broker->id) {
            throw ValidationException::withMessages([
                'brocker_id' => __('This ticket is locked. Transfer it before assigning another broker.'),
            ]);
        }

        $ticket->fill([
            'brocker_id' => $broker->id,
            'owner_id' => $broker->user_id,
            'locked_at' => now(),
            'locked_by' => $actor?->id ?? auth()->id(),
        ])->save();

        $ticket->contact->forceFill(['owner_id' => $broker->user_id])->saveQuietly();

        $this->activities->log(
            $ticket->contact,
            ActivityType::Assignment,
            __('Broker assigned'),
            __('Assigned to :name.', ['name' => $broker->user?->full_name ?? '#'.$broker->id]),
            ['brocker_id' => $broker->id],
            $ticket,
            $actor,
        );

        if ($expiresAt) {
            $this->scheduleAssignmentExpiry($ticket, $expiresAt, $broker->user_id);
        }

        $this->ensureNextAction($ticket, $broker->user_id);

        return $ticket->fresh(['contact', 'owner', 'brocker.user']);
    }

    public function transfer(PipelineTicket $ticket, Brocker $broker, ?User $actor = null): PipelineTicket
    {
        $from = $ticket->brocker?->user?->full_name ?? __('Unassigned');

        $ticket->fill([
            'brocker_id' => $broker->id,
            'owner_id' => $broker->user_id,
            'locked_at' => now(),
            'locked_by' => $actor?->id ?? auth()->id(),
        ])->save();

        $ticket->contact->forceFill(['owner_id' => $broker->user_id])->saveQuietly();

        $this->activities->log(
            $ticket->contact,
            ActivityType::Transfer,
            __('Lead transferred'),
            __('Transferred from :from to :to.', [
                'from' => $from,
                'to' => $broker->user?->full_name ?? '#'.$broker->id,
            ]),
            ['brocker_id' => $broker->id],
            $ticket,
            $actor,
        );

        $this->ensureNextAction($ticket, $broker->user_id);

        return $ticket->fresh(['contact', 'owner', 'brocker.user']);
    }

    public function unlock(PipelineTicket $ticket, ?User $actor = null): PipelineTicket
    {
        $ticket->fill([
            'locked_at' => null,
            'locked_by' => null,
        ])->save();

        $this->activities->log(
            $ticket->contact,
            ActivityType::System,
            __('Lead unlocked'),
            null,
            [],
            $ticket,
            $actor,
        );

        return $ticket;
    }

    public function attachUnit(PipelineTicket $ticket, ?InventoryUnit $unit, ?User $user = null): PipelineTicket
    {
        if ($unit && in_array($unit->status, [InventoryStatus::Sold, InventoryStatus::HandedOver], true)) {
            throw ValidationException::withMessages([
                'inventory_unit_id' => __('Sold units cannot be assigned on the pipeline. Open a deal on available stock.'),
            ]);
        }

        $previous = $ticket->inventoryUnit;
        $ticket->forceFill(['inventory_unit_id' => $unit?->id])->save();

        if ($ticket->contact) {
            $this->activities->log(
                $ticket->contact,
                ActivityType::System,
                $unit ? __('Unit assigned') : __('Unit cleared'),
                $unit
                    ? __('Pipeline ticket linked to :code. This is interest only until a hold or deal freezes stock.', ['code' => $unit->code])
                    : __('Unit unlinked from this ticket.'),
                [
                    'inventory_unit_id' => $unit?->id,
                    'previous_inventory_unit_id' => $previous?->id,
                ],
                $ticket,
                $user,
            );
        }

        return $ticket->fresh(['inventoryUnit']);
    }

    public function scheduleAssignmentExpiry(PipelineTicket $ticket, \DateTimeInterface $expiresAt, ?int $ownerId = null): CrmTask
    {
        $task = CrmTask::query()->updateOrCreate(
            [
                'pipeline_ticket_id' => $ticket->id,
                'type' => CrmTaskType::AssignmentExpiry,
                'completed_at' => null,
            ],
            [
                'contact_id' => $ticket->contact_id,
                'owner_id' => $ownerId ?? $ticket->owner_id,
                'created_by' => auth()->id(),
                'title' => __('Assignment expiry'),
                'body' => __('Follow up before the broker assignment window closes.'),
                'due_at' => $expiresAt,
                'reminder_sent_at' => null,
            ]
        );

        if ($ticket->ticketable instanceof Lead) {
            $ticket->ticketable->forceFill(['brocker_end_date' => $expiresAt])->saveQuietly();
        }

        return $task;
    }

    public function ensureNextAction(PipelineTicket $ticket, ?int $ownerId = null): CrmTask
    {
        $existing = $ticket->tasks()->whereNull('completed_at')->orderBy('due_at')->first();
        if ($existing) {
            return $existing;
        }

        return CrmTask::create([
            'contact_id' => $ticket->contact_id,
            'pipeline_ticket_id' => $ticket->id,
            'owner_id' => $ownerId ?? $ticket->owner_id,
            'created_by' => auth()->id(),
            'type' => CrmTaskType::Call,
            'title' => __('First contact'),
            'due_at' => now()->addDay(),
        ]);
    }

    private function syncLegacyStatus(PipelineTicket $ticket, PipelineStage $stage): void
    {
        $ticketable = $ticket->ticketable;
        if ($ticketable instanceof Lead) {
            $ticketable->forceFill(['status' => $stage->toLegacyLeadStatus()])->saveQuietly();
        }
        if ($ticketable instanceof SellRequest || $ticketable instanceof BuyAppartmentInstallment) {
            $status = match ($stage) {
                PipelineStage::Contacted, PipelineStage::Qualified, PipelineStage::Viewing, PipelineStage::Negotiation, PipelineStage::Reserved => 'contacted',
                PipelineStage::Won => 'approved',
                PipelineStage::Lost => 'rejected',
                default => 'pending',
            };
            $ticketable->forceFill(['status' => $status])->saveQuietly();
        }
    }
}
