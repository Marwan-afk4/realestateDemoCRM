<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\BuyerInstallmentStatus;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Models\BuyerInstallment;
use App\Services\Crm\SaleDeskService;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    use RespondsJson;

    public function index(Request $request)
    {
        abort_unless($request->user()?->can('view-collections') || $request->user()?->can('view-deals'), 403);

        $installments = BuyerInstallment::query()
            ->with(['plan.deal.contact', 'plan.inventoryUnit', 'receipts'])
            ->when($request->filter === 'overdue' || ! $request->filter, fn ($q) => $q->where('status', BuyerInstallmentStatus::Overdue))
            ->when($request->filter === 'due', fn ($q) => $q->where('status', BuyerInstallmentStatus::Pending)->where('due_date', '<=', now()->addDays(7)))
            ->when($request->filter === 'all', fn ($q) => $q)
            ->orderBy('due_date')
            ->paginate(40);

        return $this->paginated($installments, fn (BuyerInstallment $row) => [
            'id' => $row->id,
            'sequence' => $row->sequence,
            'label' => $row->label,
            'due_date' => $row->due_date?->toDateString(),
            'amount' => $row->amount,
            'paid_amount' => $row->paid_amount,
            'remaining' => $row->remaining(),
            'status' => $row->status?->value,
            'status_label' => $row->status?->label(),
            'deal_id' => $row->plan?->deal_id,
            'contact' => $row->plan?->deal?->contact ? [
                'id' => $row->plan->deal->contact->id,
                'name' => $row->plan->deal->contact->name,
                'phone' => $row->plan->deal->contact->phone,
            ] : null,
            'unit' => $row->plan?->inventoryUnit?->code,
        ]);
    }

    public function receipt(Request $request, BuyerInstallment $buyer_installment, SaleDeskService $sales)
    {
        abort_unless($request->user()?->can('view-collections') || $request->user()?->can('view-deals'), 403);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'received_at' => 'nullable|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $sales->recordReceipt($buyer_installment, $data, $request->user());

        return $this->ok([
            'id' => $buyer_installment->fresh()->id,
            'paid_amount' => $buyer_installment->fresh()->paid_amount,
            'status' => $buyer_installment->fresh()->status?->value,
            'remaining' => $buyer_installment->fresh()->remaining(),
        ], __('Receipt recorded.'));
    }
}
