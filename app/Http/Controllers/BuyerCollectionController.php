<?php

namespace App\Http\Controllers;

use App\Enums\BuyerInstallmentStatus;
use App\Models\BuyerInstallment;
use App\Services\Crm\SaleDeskService;
use Illuminate\Http\Request;

class BuyerCollectionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->can('view-collections') || $request->user()?->can('view-deals'), 403);

        $installments = BuyerInstallment::query()
            ->with(['plan.deal.contact', 'plan.inventoryUnit'])
            ->when($request->filter === 'overdue' || ! $request->filter, fn ($q) => $q->where('status', BuyerInstallmentStatus::Overdue))
            ->when($request->filter === 'due', fn ($q) => $q->where('status', BuyerInstallmentStatus::Pending)->where('due_date', '<=', now()->addDays(7)))
            ->when($request->filter === 'all', fn ($q) => $q)
            ->orderBy('due_date')
            ->paginate(40);

        return view('collections.index', compact('installments'));
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

        return back()->with('success', __('Receipt recorded.'));
    }
}
