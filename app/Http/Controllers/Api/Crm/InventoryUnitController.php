<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\InventoryStatus;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\InventoryUnit;
use App\Models\Uptown;
use App\Services\Crm\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryUnitController extends Controller
{
    use RespondsJson;

    public function index(Request $request)
    {
        abort_unless($request->user()?->can('view-inventory') || $request->user()?->can('view-uptowns'), 403);

        $units = InventoryUnit::query()
            ->with(['compound', 'developer', 'uptown', 'activeDeal.contact', 'activeHold'])
            ->when($request->keyword, function ($query, $keyword) {
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('code', 'like', "%{$keyword}%")
                        ->orWhere('unit_number', 'like', "%{$keyword}%")
                        ->orWhere('building', 'like', "%{$keyword}%")
                        ->orWhere('phase', 'like', "%{$keyword}%");
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->compound_id, fn ($q, $id) => $q->where('compound_id', $id))
            ->latest()
            ->paginate(40);

        return $this->paginated($units, fn (InventoryUnit $unit) => $this->summary($unit));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()?->can('view-inventory') || $request->user()?->can('view-uptowns'), 403);

        $data = $this->validated($request);
        $uptown = ! empty($data['uptown_id']) ? Uptown::find($data['uptown_id']) : null;
        if ($uptown) {
            $data['developer_id'] = $data['developer_id'] ?? $uptown->developer_id;
            $data['compound_id'] = $data['compound_id'] ?? $uptown->compound_id;
            $data['list_price'] = $data['list_price'] ?? $uptown->strat_price;
        }
        $data['current_price'] = $data['current_price'] ?? $data['list_price'] ?? null;
        $data['status'] = InventoryStatus::Available;

        $unit = InventoryUnit::create($data);

        return $this->created($this->detail($unit->fresh(['compound', 'developer', 'uptown'])), __('Physical unit created.'));
    }

    public function show(InventoryUnit $inventory_unit)
    {
        abort_unless(auth()->user()?->can('view-inventory') || auth()->user()?->can('view-uptowns'), 403);

        $inventory_unit->load([
            'compound.developer',
            'uptown',
            'activeDeal.contact',
            'activeHold',
            'holds.deal.contact',
            'deals.contact',
            'pipelineTickets.contact',
        ]);

        return $this->ok($this->detail($inventory_unit));
    }

    public function update(Request $request, InventoryUnit $inventory_unit)
    {
        abort_unless($request->user()?->can('view-inventory') || $request->user()?->can('view-uptowns'), 403);

        $data = $this->validated($request, $inventory_unit);
        unset($data['status']);
        $inventory_unit->update($data);

        return $this->ok($this->detail($inventory_unit->fresh(['compound', 'developer', 'uptown'])), __('Updated successfully.'));
    }

    public function release(Request $request, InventoryUnit $inventory_unit, InventoryService $inventoryService)
    {
        abort_unless($request->user()?->can('view-inventory') || $request->user()?->can('view-deals'), 403);

        $inventoryService->release($inventory_unit, $inventory_unit->activeDeal, 'manual');

        return $this->ok($this->detail($inventory_unit->fresh(['compound', 'developer', 'uptown', 'activeDeal', 'activeHold'])), __('Hold released. Unit is available.'));
    }

    private function validated(Request $request, ?InventoryUnit $unit = null): array
    {
        $data = $request->validate([
            'uptown_id' => 'nullable|exists:uptowns,id',
            'developer_id' => 'nullable|exists:developers,id',
            'compound_id' => 'required|exists:compounds,id',
            'phase' => 'nullable|string|max:40',
            'building' => 'nullable|string|max:40',
            'floor' => 'nullable|string|max:20',
            'unit_number' => [
                'required',
                'string',
                'max:40',
                Rule::unique('inventory_units')->where(fn ($query) => $query
                    ->where('compound_id', $request->compound_id)
                    ->where('phase', $request->input('phase') ?: '')
                    ->where('building', $request->input('building') ?: '')
                    ->where('floor', $request->input('floor') ?: '')
                )->ignore($unit?->id),
            ],
            'list_price' => 'nullable|numeric|min:0',
            'current_price' => 'nullable|numeric|min:0',
        ]);

        $data['phase'] = $data['phase'] ?? '';
        $data['building'] = $data['building'] ?? '';
        $data['floor'] = $data['floor'] ?? '';

        return $data;
    }

    private function summary(InventoryUnit $unit): array
    {
        return [
            'id' => $unit->id,
            'code' => $unit->code,
            'unit_number' => $unit->unit_number,
            'phase' => $unit->phase,
            'building' => $unit->building,
            'floor' => $unit->floor,
            'address' => $unit->address(),
            'status' => $unit->status?->value,
            'status_label' => $unit->status?->label(),
            'list_price' => $unit->list_price,
            'current_price' => $unit->current_price,
            'price' => $unit->price(),
            'compound' => $unit->compound ? ['id' => $unit->compound->id, 'name' => $unit->compound->compound_name] : null,
            'developer' => $unit->developer?->only(['id', 'name_en', 'name_ar']),
            'active_deal_id' => $unit->active_deal_id,
            'reserved_until' => $unit->reserved_until?->toIso8601String(),
        ];
    }

    private function detail(InventoryUnit $unit): array
    {
        return array_merge($this->summary($unit), [
            'uptown_id' => $unit->uptown_id,
            'developer_id' => $unit->developer_id,
            'compound_id' => $unit->compound_id,
            'active_hold' => $unit->activeHold,
            'holds' => $unit->relationLoaded('holds') ? $unit->holds : [],
            'deals' => $unit->relationLoaded('deals') ? $unit->deals->map(fn ($deal) => [
                'id' => $deal->id,
                'status' => $deal->status?->value ?? $deal->status,
                'contact' => $deal->contact?->name,
            ]) : [],
        ]);
    }
}
