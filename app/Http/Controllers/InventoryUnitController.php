<?php

namespace App\Http\Controllers;

use App\Enums\InventoryStatus;
use App\Models\Compound;
use App\Models\Developer;
use App\Models\InventoryUnit;
use App\Models\Uptown;
use App\Services\Crm\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryUnitController extends Controller
{
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

        $statuses = InventoryStatus::labels();
        $compounds = Compound::orderBy('compound_name')->pluck('compound_name', 'id')->toArray();

        return view('inventory.index', compact('units', 'statuses', 'compounds'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->can('view-inventory') || auth()->user()?->can('view-uptowns'), 403);

        return view('inventory.create', $this->formData());
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

        return redirect()->route('inventory-units.show', $unit)->with('success', __('Physical unit created.'));
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

        return view('inventory.show', ['unit' => $inventory_unit]);
    }

    public function edit(InventoryUnit $inventory_unit)
    {
        abort_unless(auth()->user()?->can('view-inventory') || auth()->user()?->can('view-uptowns'), 403);

        return view('inventory.edit', array_merge($this->formData(), ['unit' => $inventory_unit]));
    }

    public function update(Request $request, InventoryUnit $inventory_unit)
    {
        abort_unless($request->user()?->can('view-inventory') || $request->user()?->can('view-uptowns'), 403);

        $data = $this->validated($request, $inventory_unit);
        unset($data['status']);
        $inventory_unit->update($data);

        return redirect()->route('inventory-units.show', $inventory_unit)->with('success', __('Updated successfully.'));
    }

    public function release(Request $request, InventoryUnit $inventory_unit, InventoryService $inventoryService)
    {
        abort_unless($request->user()?->can('view-inventory') || $request->user()?->can('view-deals'), 403);

        $inventoryService->release($inventory_unit, $inventory_unit->activeDeal, 'manual');

        return back()->with('success', __('Hold released. Unit is available.'));
    }

    private function formData(): array
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';

        return [
            'developers' => Developer::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray(),
            'compounds' => Compound::orderBy('compound_name')->pluck('compound_name', 'id')->toArray(),
            'listings' => Uptown::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray(),
        ];
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
}
