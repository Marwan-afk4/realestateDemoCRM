<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\LostReason;
use App\Enums\PipelineStage;
use App\Enums\PipelineTicketType;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\InventoryUnit;
use App\Models\PipelineTicket;
use App\Services\Crm\PipelineService;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PipelineController extends Controller
{
    use RespondsJson;

    public function __construct(private SalesVisibility $visibility, private PipelineService $pipeline)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', PipelineTicket::class);

        $tickets = $this->visibility->scopeTickets(
            PipelineTicket::query()->with(['contact', 'owner', 'brocker.user', 'tasks', 'inventoryUnit.compound']),
            $request->user()
        )
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->owner_id, fn ($q, $owner) => $q->where('owner_id', $owner))
            ->when($request->stage, fn ($q, $stage) => $q->where('stage', $stage))
            ->when($request->keyword, function ($query, $keyword) {
                $query->whereHas('contact', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('updated_at')
            ->get();

        $grouped = $tickets
            ->groupBy(fn (PipelineTicket $ticket) => $ticket->stage->value)
            ->map(fn ($rows) => $rows->map(fn (PipelineTicket $ticket) => $this->serialize($ticket))->values());

        $columns = collect(PipelineStage::cases())->map(fn (PipelineStage $stage) => [
            'stage' => $stage->value,
            'label' => $stage->label(),
            'count' => $tickets->where('stage', $stage)->count(),
            'tickets' => $grouped->get($stage->value, collect())->values(),
        ]);

        return $this->ok([
            'columns' => $columns->values(),
            'types' => $this->enumOptions(PipelineTicketType::labels()),
            'lost_reasons' => $this->enumOptions(LostReason::labels()),
        ]);
    }

    public function show(PipelineTicket $pipeline)
    {
        $this->authorize('view', $pipeline);

        $pipeline->load(['contact', 'owner', 'brocker.user', 'tasks.owner', 'inventoryUnit.compound', 'activities.user']);

        return $this->ok($this->serialize($pipeline, true));
    }

    public function updateStage(Request $request, PipelineTicket $pipeline)
    {
        $this->authorize('update', $pipeline);

        $data = $request->validate([
            'stage' => ['required', Rule::enum(PipelineStage::class)],
            'lost_reason' => ['nullable', Rule::enum(LostReason::class)],
            'lost_note' => 'nullable|string|max:1000',
        ]);

        $ticket = $this->pipeline->changeStage(
            $pipeline,
            PipelineStage::from($data['stage']),
            $request->user(),
            isset($data['lost_reason']) ? LostReason::from($data['lost_reason']) : null,
            $data['lost_note'] ?? null,
        );

        return $this->ok($this->serialize($ticket->fresh(['contact', 'owner', 'brocker.user', 'inventoryUnit.compound'])), __('Stage updated.'));
    }

    public function assign(Request $request, PipelineTicket $pipeline)
    {
        $this->authorize('transfer', $pipeline);

        $data = $request->validate([
            'brocker_id' => 'required|exists:brockers,id',
            'expires_at' => 'nullable|date',
        ]);

        $broker = Brocker::findOrFail($data['brocker_id']);
        $expires = ! empty($data['expires_at']) ? new \DateTimeImmutable($data['expires_at']) : null;

        if ($pipeline->brocker_id && $pipeline->brocker_id !== $broker->id) {
            $ticket = $this->pipeline->transfer($pipeline, $broker, $request->user());
        } else {
            $ticket = $this->pipeline->assign($pipeline, $broker, $request->user(), $expires);
        }

        if ($expires) {
            $this->pipeline->scheduleAssignmentExpiry($ticket, $expires, $broker->user_id);
        }

        return $this->ok($this->serialize($ticket->fresh(['contact', 'owner', 'brocker.user'])), __('Assignment updated.'));
    }

    public function unlock(PipelineTicket $pipeline)
    {
        $this->authorize('transfer', $pipeline);
        $ticket = $this->pipeline->unlock($pipeline, auth()->user());

        return $this->ok($this->serialize($ticket->fresh(['contact', 'owner', 'brocker.user'])), __('Lead unlocked.'));
    }

    public function attachUnit(Request $request, PipelineTicket $pipeline)
    {
        $this->authorize('update', $pipeline);

        $data = $request->validate([
            'inventory_unit_id' => 'nullable|exists:inventory_units,id',
        ]);

        $unit = ! empty($data['inventory_unit_id'])
            ? InventoryUnit::findOrFail($data['inventory_unit_id'])
            : null;

        $ticket = $this->pipeline->attachUnit($pipeline, $unit, $request->user());

        return $this->ok(
            $this->serialize($ticket->fresh(['contact', 'owner', 'brocker.user', 'inventoryUnit.compound'])),
            $unit ? __('Unit assigned to this pipeline ticket.') : __('Unit cleared from this ticket.')
        );
    }

    private function serialize(PipelineTicket $ticket, bool $detailed = false): array
    {
        $openTask = $ticket->relationLoaded('tasks')
            ? $ticket->tasks->first(fn ($task) => $task->completed_at === null)
            : $ticket->openTask();

        $data = [
            'id' => $ticket->id,
            'type' => $ticket->type?->value,
            'type_label' => $ticket->type?->label(),
            'stage' => $ticket->stage?->value,
            'stage_label' => $ticket->stage?->label(),
            'probability' => $ticket->probability,
            'lost_reason' => $ticket->lost_reason?->value,
            'lost_note' => $ticket->lost_note,
            'locked' => $ticket->isLocked(),
            'contact' => $ticket->contact ? [
                'id' => $ticket->contact->id,
                'name' => $ticket->contact->name,
                'phone' => $ticket->contact->phone,
                'email' => $ticket->contact->email,
                'whatsapp_link' => $ticket->contact->whatsappLink(),
                'tel_link' => $ticket->contact->telLink(),
            ] : null,
            'owner' => $this->userSummary($ticket->owner),
            'broker' => $ticket->brocker ? [
                'id' => $ticket->brocker->id,
                'name' => $ticket->brocker->user?->full_name,
            ] : null,
            'inventory_unit' => $ticket->inventoryUnit ? [
                'id' => $ticket->inventoryUnit->id,
                'code' => $ticket->inventoryUnit->code,
                'address' => $ticket->inventoryUnit->address(),
                'status' => $ticket->inventoryUnit->status?->value,
            ] : null,
            'open_task' => $openTask ? [
                'id' => $openTask->id,
                'title' => $openTask->title,
                'due_at' => $openTask->due_at?->toIso8601String(),
                'overdue' => $openTask->isOverdue(),
            ] : null,
            'ticketable_type' => $ticket->ticketable_type,
            'ticketable_id' => $ticket->ticketable_id,
            'stage_changed_at' => $ticket->stage_changed_at?->toIso8601String(),
            'updated_at' => $ticket->updated_at?->toIso8601String(),
        ];

        if ($detailed) {
            $data['activities'] = $ticket->activities->map(fn ($activity) => [
                'id' => $activity->id,
                'type' => $activity->type?->value,
                'title' => $activity->title,
                'body' => $activity->body,
                'user' => $this->userSummary($activity->user),
                'created_at' => $activity->created_at?->toIso8601String(),
            ]);
        }

        return $data;
    }
}
