<?php

namespace App\Services\Crm;

use App\Enums\AfterSalesTicketStatus;
use App\Enums\AfterSalesTicketType;
use App\Models\AfterSalesTicket;
use App\Models\Deal;
use App\Models\InventoryUnit;
use App\Models\User;

class AfterSalesService
{
    public function openFromHandover(Deal $deal, InventoryUnit $unit, ?User $user = null): AfterSalesTicket
    {
        return AfterSalesTicket::create([
            'deal_id' => $deal->id,
            'inventory_unit_id' => $unit->id,
            'contact_id' => $deal->contact_id,
            'developer_id' => $deal->developer_id ?? $unit->developer_id,
            'compound_id' => $deal->compound_id ?? $unit->compound_id,
            'type' => AfterSalesTicketType::Snagging,
            'status' => AfterSalesTicketStatus::Open,
            'priority' => 'normal',
            'title' => __('Snagging — :code', ['code' => $unit->code]),
            'description' => __('Auto-opened when unit was marked handed over.'),
            'created_by' => $user?->id ?? auth()->id(),
        ]);
    }

    public function transition(AfterSalesTicket $ticket, AfterSalesTicketStatus $status): AfterSalesTicket
    {
        $ticket->status = $status;

        if ($status === AfterSalesTicketStatus::Resolved) {
            $ticket->resolved_at = now();
        }

        if ($status === AfterSalesTicketStatus::Closed) {
            $ticket->closed_at = now();
            $ticket->resolved_at = $ticket->resolved_at ?? now();
        }

        $ticket->save();

        return $ticket->fresh(['contact', 'deal', 'inventoryUnit', 'assignee']);
    }
}
