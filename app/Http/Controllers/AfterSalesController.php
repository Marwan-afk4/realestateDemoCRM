<?php

namespace App\Http\Controllers;

use App\Enums\AfterSalesTicketStatus;
use App\Enums\AfterSalesTicketType;
use App\Models\AfterSalesTicket;
use App\Models\Deal;
use App\Models\User;
use App\Services\Crm\AfterSalesService;
use App\Services\Crm\DeveloperVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AfterSalesController extends Controller
{
    public function __construct(
        private AfterSalesService $afterSales,
        private DeveloperVisibility $developerVisibility,
    ) {
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

        return view('after-sales.index', [
            'tickets' => $tickets,
            'types' => AfterSalesTicketType::labels(),
            'statuses' => AfterSalesTicketStatus::labels(),
        ]);
    }

    public function show(AfterSalesTicket $after_sales_ticket)
    {
        abort_unless(auth()->user()?->can('view-after-sales'), 403);
        $this->authorizeTicket($after_sales_ticket);

        $after_sales_ticket->load(['contact', 'deal', 'inventoryUnit.compound', 'assignee', 'developer']);

        return view('after-sales.show', ['ticket' => $after_sales_ticket]);
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

        AfterSalesTicket::create([
            ...$data,
            'developer_id' => $deal?->developer_id,
            'compound_id' => $deal?->compound_id,
            'contact_id' => $data['contact_id'] ?? $deal?->contact_id,
            'inventory_unit_id' => $data['inventory_unit_id'] ?? $deal?->inventory_unit_id,
            'status' => isset($data['scheduled_at']) ? AfterSalesTicketStatus::Scheduled : AfterSalesTicketStatus::Open,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('after-sales.index')->with('success', __('After-sales ticket created.'));
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

        $this->afterSales->transition($after_sales_ticket, AfterSalesTicketStatus::from($data['status']));

        return back()->with('success', __('Ticket updated.'));
    }

    private function authorizeTicket(AfterSalesTicket $ticket): void
    {
        $user = auth()->user();
        if ($user->role === 'developer' && $ticket->developer_id !== $user->developer_id) {
            abort(403);
        }
    }
}
