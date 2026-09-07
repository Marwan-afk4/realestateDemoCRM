<?php

namespace App\Services\Crm;

use App\Models\Deal;
use App\Models\InventoryUnit;
use App\Models\Lead;
use App\Models\PipelineTicket;
use App\Models\Uptown;
use Illuminate\Validation\ValidationException;

class DealFormService
{
    public function __construct(private InventoryService $inventory)
    {
    }

    public function prepare(array $data, ?Deal $deal = null): array
    {
        return $this->attachInventory($this->mergeTicket($data, $deal), $deal);
    }

    public function mergeTicket(array $data, ?Deal $deal = null): array
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

    public function mergeListerBroker(array $data, ?Deal $deal = null): array
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

    public function attachInventory(array $data, ?Deal $deal = null): array
    {
        if (empty($data['inventory_unit_id']) && ! empty($data['uptown_id'])) {
            $uptown = Uptown::query()->find($data['uptown_id']);
            if ($uptown) {
                $unit = $this->inventory->ensureForUptown($uptown);
                $data['inventory_unit_id'] = $unit->id;
            }
        }

        if (! empty($data['inventory_unit_id'])) {
            $unit = InventoryUnit::query()->find($data['inventory_unit_id']);
            if ($unit) {
                if ($unit->status->blocksOtherSale() && $unit->active_deal_id !== $deal?->id) {
                    throw ValidationException::withMessages([
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
}
