<?php

namespace App\Http\Controllers;

use App\Enums\LostReason;
use App\Enums\PipelineStage;
use App\Enums\PipelineTicketType;
use App\Models\Brocker;
use App\Models\PipelineTicket;
use App\Services\Crm\PipelineService;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PipelineController extends Controller
{
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
            ->when($request->keyword, function ($query, $keyword) {
                $query->whereHas('contact', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('updated_at')
            ->get()
            ->groupBy(fn (PipelineTicket $ticket) => $ticket->stage->value);

        $stages = PipelineStage::cases();
        $types = PipelineTicketType::labels();
        $brokers = Brocker::with('user')->get();
        $lostReasons = LostReason::labels();
        $inventoryUnits = \App\Models\InventoryUnit::query()
            ->with('compound')
            ->whereNotIn('status', [\App\Enums\InventoryStatus::Sold->value, \App\Enums\InventoryStatus::HandedOver->value])
            ->orderBy('code')
            ->get()
            ->mapWithKeys(fn ($unit) => [$unit->id => $unit->code.' — '.$unit->address().' ('.$unit->status->label().')']);

        return view('pipeline.index', compact('tickets', 'stages', 'types', 'brokers', 'lostReasons', 'inventoryUnits'));
    }

    public function show(PipelineTicket $pipeline)
    {
        $this->authorize('view', $pipeline);

        return redirect()->route('contacts.show', $pipeline->contact_id);
    }

    public function updateStage(Request $request, PipelineTicket $pipeline)
    {
        $this->authorize('update', $pipeline);

        $data = $request->validate([
            'stage' => ['required', Rule::enum(PipelineStage::class)],
            'lost_reason' => ['nullable', Rule::enum(LostReason::class)],
            'lost_note' => 'nullable|string|max:1000',
        ]);

        $this->pipeline->changeStage(
            $pipeline,
            PipelineStage::from($data['stage']),
            $request->user(),
            isset($data['lost_reason']) ? LostReason::from($data['lost_reason']) : null,
            $data['lost_note'] ?? null,
        );

        return back()->with('success', __('Stage updated.'));
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
            $this->pipeline->transfer($pipeline, $broker, $request->user());
        } else {
            $this->pipeline->assign($pipeline, $broker, $request->user(), $expires);
        }

        if ($expires) {
            $this->pipeline->scheduleAssignmentExpiry($pipeline, $expires, $broker->user_id);
        }

        return back()->with('success', __('Assignment updated.'));
    }

    public function unlock(PipelineTicket $pipeline)
    {
        $this->authorize('transfer', $pipeline);
        $this->pipeline->unlock($pipeline, auth()->user());

        return back()->with('success', __('Lead unlocked.'));
    }

    public function attachUnit(Request $request, PipelineTicket $pipeline)
    {
        $this->authorize('update', $pipeline);

        $data = $request->validate([
            'inventory_unit_id' => 'nullable|exists:inventory_units,id',
        ]);

        $unit = ! empty($data['inventory_unit_id'])
            ? \App\Models\InventoryUnit::findOrFail($data['inventory_unit_id'])
            : null;

        $this->pipeline->attachUnit($pipeline, $unit, $request->user());

        return back()->with('success', $unit ? __('Unit assigned to this pipeline ticket.') : __('Unit cleared from this ticket.'));
    }
}
