<?php

namespace App\Http\Controllers;

use App\Enums\DealStatuses;
use App\Models\Brocker;
use App\Models\Deal;
use App\Models\Developer;
use App\Models\Compound;
use App\Models\InventoryUnit;
use App\Models\Lead;
use App\Models\PipelineTicket;
use App\Models\Uptown;
use App\Models\UptownType;
use App\Services\Crm\DealCloser;
use App\Services\Crm\InventoryService;
use App\Services\Crm\SalesVisibility;


use Illuminate\Http\Request;
use App\Http\Requests\StoreDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Http\Controllers\Controller;

class DealController extends Controller
{

    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'ASC');
        $keyword   = $request->get('keyword');

        $dealsQuery = Deal::with(['developer', 'compound', 'uptownType', 'uptown', 'brocker.user', 'lead', 'contact', 'inventoryUnit'])
            ->when($request->developer_id, fn($q) => $q->where('developer_id', $request->developer_id))
            ->when($request->compound_id, fn($q) => $q->where('compound_id', $request->compound_id))
            ->when($request->uptown_type_id, fn($q) => $q->where('uptown_type_id', $request->uptown_type_id))
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('fullname', 'like', "%{$keyword}%")
                        ->orWhere('number_of_units', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%")
                        ->orWhereHas('developer', fn($sub) => $sub->where('name_en', 'like', "%{$keyword}%")->orWhere('name_ar', 'like', "%{$keyword}%"))
                        ->orWhereHas('compound', fn($sub) => $sub->where('compound_name', 'like', "%{$keyword}%"))
                        ->orWhereHas('uptownType', fn($sub) => $sub->where('name_en', 'like', "%{$keyword}%")->orWhere('name_ar', 'like', "%{$keyword}%"));
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy($sortField, $sortOrder);

        app(SalesVisibility::class)->scopeDeals($dealsQuery, $request->user());

        $deals = $dealsQuery->paginate(30);

        // Count per status
        $dealsStatusCounts = Deal::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statuses = DealStatuses::cases(); // Get all enum cases

        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $developers = Developer::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $compounds = Compound::orderBy('id')->pluck('id', 'id')->toArray();
        $uptownTypes = UptownType::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();

        return view('deals.index', compact(
            'deals',
            'sortField',
            'sortOrder',
            'developers',
            'compounds',
            'uptownTypes',
            'statuses',
            'dealsStatusCounts'
        ));
    }



    public function create(Request $request)
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $developers = Developer::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $compounds = Compound::orderBy('compound_name')->pluck('compound_name', 'id')->toArray();
        $uptownTypes = UptownType::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $units = Uptown::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $inventoryUnits = $this->inventoryOptions();
        $brokers = Brocker::with('user')->get()->mapWithKeys(fn ($b) => [$b->id => $b->user?->full_name ?? '#'.$b->id])->toArray();
        $leads = Lead::orderByDesc('id')->limit(200)->get()->mapWithKeys(fn ($l) => [$l->id => $l->lead_name.' ('.$l->lead_phone.')'])->toArray();

        $statuses = DealStatuses::labels();
        $fromTicket = null;
        if ($request->filled('pipeline_ticket_id')) {
            $fromTicket = PipelineTicket::query()->with(['contact', 'inventoryUnit'])->find($request->pipeline_ticket_id);
            if ($fromTicket?->inventoryUnit && ! isset($inventoryUnits[$fromTicket->inventory_unit_id])) {
                $unit = $fromTicket->inventoryUnit;
                $inventoryUnits[$unit->id] = $unit->code.' — '.$unit->address().' ('.$unit->status->label().')';
            }
        }

        return view('deals.create', compact('developers', 'compounds', 'uptownTypes', 'statuses', 'units', 'inventoryUnits', 'brokers', 'leads', 'fromTicket'));
    }

    public function store(StoreDealRequest $request)
    {
        $data = $this->attachInventory($this->mergeTicket($request->validated()));
        $deal = Deal::create($data);
        $status = DealStatuses::tryFrom($request->input('status', 'pending')) ?? DealStatuses::Pending;
        if (in_array($status, [DealStatuses::Approved, DealStatuses::SemiDone, DealStatuses::Rejected], true)) {
            app(DealCloser::class)->applyStatus($deal, $status);
        }

        return redirect()->route('deals.index')->with('success', 'Created successfully');
    }

    public function show(Deal $deal)
    {
        $deal->load(['developer', 'compound', 'uptownType', 'uptown', 'brocker.user', 'listerBroker.user', 'lead', 'contact', 'commission.splits.user', 'inventoryUnit.compound', 'activeOffer', 'saleDocuments', 'paymentPlan.installments']);
        return view('deals.show', compact('deal'));
    }

    public function edit(Deal $deal)
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
        $developers = Developer::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $compounds = Compound::orderBy('compound_name')->pluck('compound_name', 'id')->toArray();
        $uptownTypes = UptownType::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $units = Uptown::orderBy($nameColumn)->pluck($nameColumn, 'id')->toArray();
        $inventoryUnits = $this->inventoryOptions($deal);
        $brokers = Brocker::with('user')->get()->mapWithKeys(fn ($b) => [$b->id => $b->user?->full_name ?? '#'.$b->id])->toArray();
        $leads = Lead::orderByDesc('id')->limit(200)->get()->mapWithKeys(fn ($l) => [$l->id => $l->lead_name.' ('.$l->lead_phone.')'])->toArray();

        $statuses = DealStatuses::labels();
        return view('deals.edit', compact('deal', 'developers', 'compounds', 'uptownTypes', 'statuses', 'units', 'inventoryUnits', 'brokers', 'leads'));
    }

    public function update(UpdateDealRequest $request, Deal $deal)
    {
        $data = $this->attachInventory($this->mergeTicket($request->validated(), $deal), $deal);
        $newStatus = isset($data['status']) ? DealStatuses::tryFrom($data['status']) : null;
        unset($data['status']);
        $deal->update($data);

        if ($newStatus && $newStatus !== $deal->status) {
            app(DealCloser::class)->applyStatus($deal->fresh(), $newStatus);
        } elseif ($newStatus) {
            $deal->update(['status' => $newStatus]);
        }

        return redirect()->route('deals.index')->with('success', 'Updated successfully.');
    }

    private function mergeTicket(array $data, ?Deal $deal = null): array
    {
        if (! empty($data['pipeline_ticket_id'])) {
            $ticket = PipelineTicket::query()->with('contact')->find($data['pipeline_ticket_id']);
            if ($ticket) {
                $data['contact_id'] = $data['contact_id'] ?? $ticket->contact_id;
                $data['brocker_id'] = $data['brocker_id'] ?? $ticket->brocker_id;
                $data['inventory_unit_id'] = $data['inventory_unit_id'] ?? $ticket->inventory_unit_id;
                if (empty($data['lead_id']) && $ticket->ticketable_type === Lead::class) {
                    $data['lead_id'] = $ticket->ticketable_id;
                }
            }
        }

        return $this->mergeListerBroker($data, $deal);
    }

    private function mergeListerBroker(array $data, ?Deal $deal = null): array
    {
        if (! empty($data['lister_broker_id']) || empty($data['brocker_id'])) {
            return $data;
        }

        $inventoryUnitId = $data['inventory_unit_id'] ?? $deal?->inventory_unit_id;
        if (! $inventoryUnitId) {
            return $data;
        }

        $listerBrokerId = PipelineTicket::query()
            ->where('inventory_unit_id', $inventoryUnitId)
            ->whereNotNull('brocker_id')
            ->where('brocker_id', '!=', $data['brocker_id'])
            ->orderBy('id')
            ->value('brocker_id');

        if ($listerBrokerId) {
            $data['lister_broker_id'] = $listerBrokerId;
        }

        return $data;
    }

    private function attachInventory(array $data, ?Deal $deal = null): array
    {
        if (empty($data['inventory_unit_id']) && ! empty($data['uptown_id'])) {
            $uptown = Uptown::query()->find($data['uptown_id']);
            if ($uptown) {
                $unit = app(InventoryService::class)->ensureForUptown($uptown);
                $data['inventory_unit_id'] = $unit->id;
            }
        }

        if (! empty($data['inventory_unit_id'])) {
            $unit = InventoryUnit::query()->find($data['inventory_unit_id']);
            if ($unit) {
                if ($unit->status->blocksOtherSale() && $unit->active_deal_id !== $deal?->id) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'inventory_unit_id' => __('This unit is already held, reserved, or sold.'),
                    ]);
                }
                $data['uptown_id'] = $data['uptown_id'] ?? $unit->uptown_id;
                $data['compound_id'] = $data['compound_id'] ?? $unit->compound_id;
                $data['developer_id'] = $data['developer_id'] ?? $unit->developer_id;
                if (empty($data['value'])) {
                    $data['value'] = $unit->price();
                }
            }
        }

        return $data;
    }

    private function inventoryOptions(?Deal $deal = null): array
    {
        return InventoryUnit::query()
            ->with('compound')
            ->orderBy('code')
            ->get()
            ->filter(function (InventoryUnit $unit) use ($deal) {
                if ($deal && ($unit->id === $deal->inventory_unit_id || $unit->active_deal_id === $deal->id)) {
                    return true;
                }

                return ! $unit->status->blocksOtherSale();
            })
            ->mapWithKeys(fn (InventoryUnit $unit) => [
                $unit->id => $unit->code.' — '.$unit->address().' ('.$unit->status->label().')',
            ])
            ->toArray();
    }
}
