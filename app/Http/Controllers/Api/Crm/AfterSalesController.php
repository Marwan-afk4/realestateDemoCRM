<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\AfterSalesTicketStatus;
use App\Enums\AfterSalesTicketType;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\AfterSalesTicket;
use App\Models\Deal;
use App\Services\Crm\AfterSalesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AfterSalesController extends Controller
{
    use RespondsJson;

    public function __construct(private AfterSalesService $afterSales)
    {
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->can('view-after-sales'), 403);

        $tickets = AfterSalesTicket::query()
            ->with(['contact', 'deal', 'inventoryUnit', 'assignee', 'developer'])
            ->when($request->user()->role === 'developer', function ($q) use ($request) {
                $q->where('developer_id', $request->user()->developer_id);
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->latest()
            ->paginate(30);

        return $this->paginated($tickets, fn (AfterSalesTicket $ticket) => $this->summary($ticket));
    }

    public function show(AfterSalesTicket $after_sales_ticket)
    {
        abort_unless(auth()->user()?->can('view-after-sales'), 403);
        $this->authorizeTicket($after_sales_ticket);

        $after_sales_ticket->load(['contact', 'deal', 'inventoryUnit.compound', 'assignee', 'developer']);

        return $this->ok($this->detail($after_sales_ticket));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->can('view-after-sales'), 403);

        $data = $request->validate([
            'deal_id' => 'nullable|exists:deals,id',
            'inventory_unit_id' => 'nullable|exists:inventory_units,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'type' => ['required', Rule::enum(AfterSalesTicketType::class)],
            'priority' => 'required|in:low,normal,high',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'scheduled_at' => 'nullable|date',
        ]);

        $deal = isset($data['deal_id']) ? Deal::query()->find($data['deal_id']) : null;

        $ticket = AfterSalesTicket::create([
            ...$data,
            'developer_id' => $deal?->developer_id,
            'compound_id' => $deal?->compound_id,
            'contact_id' => $data['contact_id'] ?? $deal?->contact_id,
            'inventory_unit_id' => $data['inventory_unit_id'] ?? $deal?->inventory_unit_id,
            'status' => isset($data['scheduled_at']) ? AfterSalesTicketStatus::Scheduled : AfterSalesTicketStatus::Open,
            'created_by' => $request->user()->id,
        ]);

        return $this->created($this->detail($ticket->fresh(['contact', 'deal', 'inventoryUnit', 'assignee'])), __('After-sales ticket created.'));
    }

    public function updateStatus(Request $request, AfterSalesTicket $after_sales_ticket)
    {
        abort_unless($request->user()->can('view-after-sales'), 403);
        $this->authorizeTicket($after_sales_ticket);

        $data = $request->validate([
            'status' => ['required', Rule::enum(AfterSalesTicketStatus::class)],
            'assigned_to' => 'nullable|exists:users,id',
            'scheduled_at' => 'nullable|date',
        ]);

        if (isset($data['assigned_to'])) {
            $after_sales_ticket->assigned_to = $data['assigned_to'];
        }
        if (isset($data['scheduled_at'])) {
            $after_sales_ticket->scheduled_at = $data['scheduled_at'];
        }

        $ticket = $this->afterSales->transition($after_sales_ticket, AfterSalesTicketStatus::from($data['status']));

        return $this->ok($this->detail($ticket), __('Ticket updated.'));
    }

    private function authorizeTicket(AfterSalesTicket $ticket): void
    {
        $user = auth()->user();
        if ($user->role === 'developer' && $ticket->developer_id !== $user->developer_id) {
            abort(403);
        }
    }

    private function summary(AfterSalesTicket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'type' => $ticket->type?->value,
            'type_label' => $ticket->type?->label(),
            'status' => $ticket->status?->value,
            'status_label' => $ticket->status?->label(),
            'priority' => $ticket->priority,
            'contact' => $ticket->contact ? ['id' => $ticket->contact->id, 'name' => $ticket->contact->name] : null,
            'unit' => $ticket->inventoryUnit?->code,
            'assignee' => $this->userSummary($ticket->assignee),
            'scheduled_at' => $ticket->scheduled_at?->toIso8601String(),
            'created_at' => $ticket->created_at?->toIso8601String(),
        ];
    }

    private function detail(AfterSalesTicket $ticket): array
    {
        return array_merge($this->summary($ticket), [
            'description' => $ticket->description,
            'deal_id' => $ticket->deal_id,
            'developer_id' => $ticket->developer_id,
            'compound_id' => $ticket->compound_id,
            'resolved_at' => $ticket->resolved_at?->toIso8601String(),
            'closed_at' => $ticket->closed_at?->toIso8601String(),
        ]);
    }
}
