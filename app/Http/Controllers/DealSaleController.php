<?php

namespace App\Http\Controllers;

use App\Enums\CommissionPayoutStatus;
use App\Enums\HoldType;
use App\Enums\SaleDocumentType;
use App\Models\BuyerInstallment;
use App\Models\Deal;
use App\Models\SaleOffer;
use App\Services\Crm\InventoryService;
use App\Services\Crm\SaleDeskService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DealSaleController extends Controller
{
    public function __construct(
        private InventoryService $inventory,
        private SaleDeskService $sales,
    ) {
    }

    public function hold(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);

        $data = $request->validate([
            'type' => ['required', Rule::enum(HoldType::class)],
            'expires_at' => 'nullable|date|after:now',
        ]);

        $unit = $this->inventory->resolveForDeal($deal);
        $this->inventory->placeHold(
            $unit,
            $deal->fresh(),
            HoldType::from($data['type']),
            isset($data['expires_at']) ? new \DateTimeImmutable($data['expires_at']) : null,
            $request->user(),
        );

        return back()->with('success', __('Unit hold placed.'));
    }

    public function offer(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);

        $data = $request->validate([
            'list_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,installment,mixed',
            'down_payment_percent' => 'nullable|integer|min:0|max:100',
            'installment_count' => 'nullable|integer|min:0|max:120',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $this->sales->createOffer($deal, $data, $request->user());

        return back()->with('success', __('Offer issued.'));
    }

    public function acceptOffer(Request $request, Deal $deal, SaleOffer $sale_offer)
    {
        abort_unless($request->user()?->can('view-deals'), 403);
        abort_unless($sale_offer->deal_id === $deal->id, 404);

        $this->sales->acceptOffer($sale_offer, $request->user());

        return back()->with('success', __('Offer accepted. Reservation letter issued.'));
    }

    public function document(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);

        $data = $request->validate([
            'type' => ['required', Rule::enum(SaleDocumentType::class)],
        ]);

        $this->sales->issueDocument($deal, SaleDocumentType::from($data['type']), $deal->activeOffer, $request->user());

        return back()->with('success', __('Document issued.'));
    }

    public function paymentPlan(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals') || $request->user()?->can('view-collections'), 403);

        $this->sales->ensurePaymentPlan($deal);

        return back()->with('success', __('Buyer payment plan created.'));
    }

    public function receipt(Request $request, Deal $deal, BuyerInstallment $buyer_installment)
    {
        abort_unless($request->user()?->can('view-deals') || $request->user()?->can('view-collections'), 403);
        abort_unless($buyer_installment->plan?->deal_id === $deal->id, 404);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'received_at' => 'nullable|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $this->sales->recordReceipt($buyer_installment, $data, $request->user());

        return back()->with('success', __('Receipt recorded.'));
    }

    public function handover(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);

        $unit = $this->inventory->resolveForDeal($deal);
        $this->inventory->markHandedOver($unit, $deal);

        return back()->with('success', __('Unit marked handed over.'));
    }

    public function payout(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);

        $data = $request->validate([
            'payout_status' => ['required', Rule::enum(CommissionPayoutStatus::class)],
        ]);

        $commission = $deal->commission;
        abort_unless($commission, 404);

        $status = CommissionPayoutStatus::from($data['payout_status']);
        $commission->payout_status = $status;
        $commission->paid_at = $status === CommissionPayoutStatus::Paid ? now() : null;
        $commission->save();

        return back()->with('success', __('Commission payout updated.'));
    }
}
