<?php

namespace App\Http\Controllers\Api\Crm;

use App\Enums\ActivityType;
use App\Enums\CommissionPayoutStatus;
use App\Enums\DealStatuses;
use App\Enums\HoldType;
use App\Enums\SaleDocumentType;
use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDealRequest;
use App\Http\Requests\UpdateDealRequest;
use App\Models\BuyerInstallment;
use App\Models\Deal;
use App\Models\SaleOffer;
use App\Services\Crm\ActivityLogger;
use App\Services\Crm\AfterSalesService;
use App\Services\Crm\DealCloser;
use App\Services\Crm\DealFormService;
use App\Services\Crm\InventoryService;
use App\Services\Crm\SaleDeskService;
use App\Services\Crm\SalesVisibility;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    use RespondsJson;

    public function __construct(
        private SalesVisibility $visibility,
        private DealFormService $form,
        private InventoryService $inventory,
        private SaleDeskService $sales,
        private ActivityLogger $activities,
        private AfterSalesService $afterSales,
    ) {
    }

    public function index(Request $request)
    {
        abort_unless($request->user()?->can('view-deals'), 403);

        $deals = $this->visibility->scopeDeals(
            Deal::query()->with(['developer', 'compound', 'uptownType', 'uptown', 'brocker.user', 'lead', 'contact', 'inventoryUnit']),
            $request->user()
        )
            ->when($request->developer_id, fn ($q) => $q->where('developer_id', $request->developer_id))
            ->when($request->compound_id, fn ($q) => $q->where('compound_id', $request->compound_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('fullname', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(30);

        return $this->paginated($deals, fn (Deal $deal) => $this->summary($deal));
    }

    public function store(StoreDealRequest $request)
    {
        abort_unless($request->user()?->can('view-deals'), 403);

        $data = $this->form->prepare($request->validated());
        $deal = Deal::create($data);
        $status = DealStatuses::tryFrom($request->input('status', 'pending')) ?? DealStatuses::Pending;
        if (in_array($status, [DealStatuses::Approved, DealStatuses::SemiDone, DealStatuses::Rejected], true)) {
            app(DealCloser::class)->applyStatus($deal, $status);
        }

        return $this->created($this->detail($deal->fresh($this->relations())), __('Created successfully'));
    }

    public function show(Deal $deal)
    {
        abort_unless(request()->user()?->can('view-deals'), 403);
        $this->assertVisible($deal);

        return $this->ok($this->detail($deal->load($this->relations())));
    }

    public function update(UpdateDealRequest $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);
        $this->assertVisible($deal);

        $data = $this->form->prepare($request->validated(), $deal);
        $newStatus = isset($data['status']) ? DealStatuses::tryFrom($data['status']) : null;
        unset($data['status']);
        $deal->update($data);

        if ($newStatus && $newStatus !== $deal->status) {
            app(DealCloser::class)->applyStatus($deal->fresh(), $newStatus);
        } elseif ($newStatus) {
            $deal->update(['status' => $newStatus]);
        }

        return $this->ok($this->detail($deal->fresh($this->relations())), __('Updated successfully.'));
    }

    public function hold(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);
        $this->assertVisible($deal);

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

        return $this->ok($this->detail($deal->fresh($this->relations())), __('Unit hold placed.'));
    }

    public function offer(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);
        $this->assertVisible($deal);

        $data = $request->validate([
            'list_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,installment,mixed',
            'down_payment_percent' => 'nullable|integer|min:0|max:100',
            'installment_count' => 'nullable|integer|min:0|max:120',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $offer = $this->sales->createOffer($deal, $data, $request->user());

        return $this->ok([
            'deal' => $this->detail($deal->fresh($this->relations())),
            'offer' => $this->offerSummary($offer),
        ], __('Offer issued.'));
    }

    public function acceptOffer(Request $request, Deal $deal, SaleOffer $sale_offer)
    {
        abort_unless($request->user()?->can('view-deals'), 403);
        abort_unless($sale_offer->deal_id === $deal->id, 404);
        $this->assertVisible($deal);

        $this->sales->acceptOffer($sale_offer, $request->user());

        return $this->ok($this->detail($deal->fresh($this->relations())), __('Offer accepted. Reservation letter issued.'));
    }

    public function document(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);
        $this->assertVisible($deal);

        $data = $request->validate([
            'type' => ['required', Rule::enum(SaleDocumentType::class)],
        ]);

        $this->sales->issueDocument($deal, SaleDocumentType::from($data['type']), $deal->activeOffer, $request->user());

        return $this->ok($this->detail($deal->fresh($this->relations())), __('Document issued.'));
    }

    public function paymentPlan(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals') || $request->user()?->can('view-collections'), 403);
        $this->assertVisible($deal);

        $plan = $this->sales->ensurePaymentPlan($deal);

        return $this->ok([
            'deal' => $this->detail($deal->fresh($this->relations())),
            'plan' => $plan,
        ], __('Buyer payment plan created.'));
    }

    public function receipt(Request $request, Deal $deal, BuyerInstallment $buyer_installment)
    {
        abort_unless($request->user()?->can('view-deals') || $request->user()?->can('view-collections'), 403);
        abort_unless($buyer_installment->plan?->deal_id === $deal->id, 404);
        $this->assertVisible($deal);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'received_at' => 'nullable|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $this->sales->recordReceipt($buyer_installment, $data, $request->user());

        return $this->ok($this->detail($deal->fresh($this->relations())), __('Receipt recorded.'));
    }

    public function handover(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);
        $this->assertVisible($deal);

        $unit = $this->inventory->resolveForDeal($deal);
        $this->inventory->markHandedOver($unit, $deal);
        $ticket = $this->afterSales->openFromHandover($deal->fresh(['contact']), $unit->fresh(), $request->user());

        return $this->ok([
            'deal' => $this->detail($deal->fresh($this->relations())),
            'after_sales_ticket_id' => $ticket->id,
        ], __('Unit marked handed over. Snagging ticket opened.'));
    }

    public function payout(Request $request, Deal $deal)
    {
        abort_unless($request->user()?->can('view-deals'), 403);
        $this->assertVisible($deal);

        $data = $request->validate([
            'payout_status' => ['required', Rule::enum(CommissionPayoutStatus::class)],
        ]);

        $commission = $deal->commission;
        abort_unless($commission, 404);

        $previous = $commission->payout_status;
        $status = CommissionPayoutStatus::from($data['payout_status']);
        $commission->payout_status = $status;
        $commission->paid_at = $status === CommissionPayoutStatus::Paid ? ($commission->paid_at ?? now()) : null;
        $commission->save();

        if ($deal->contact && $previous !== $status) {
            $this->activities->log(
                $deal->contact,
                ActivityType::System,
                __('Commission payout updated'),
                __('Payout status is now :status.', ['status' => $status->label()]),
                ['deal_id' => $deal->id, 'payout_status' => $status->value],
                $deal->ticket,
                $request->user(),
            );
        }

        return $this->ok($this->detail($deal->fresh($this->relations())), __('Commission payout updated.'));
    }

    private function assertVisible(Deal $deal): void
    {
        $visible = $this->visibility->scopeDeals(Deal::query(), request()->user())->whereKey($deal->id)->exists();
        abort_unless($visible, 403);
    }

    private function relations(): array
    {
        return [
            'developer', 'compound', 'uptownType', 'uptown', 'brocker.user', 'listerBroker.user',
            'lead', 'contact', 'commission.splits.user', 'inventoryUnit.compound',
            'activeOffer', 'offers', 'saleDocuments', 'paymentPlan.installments.receipts', 'ticket',
        ];
    }

    private function summary(Deal $deal): array
    {
        return [
            'id' => $deal->id,
            'fullname' => $deal->fullname,
            'phone' => $deal->phone,
            'email' => $deal->email,
            'status' => $deal->status?->value ?? $deal->status,
            'status_label' => $deal->status instanceof DealStatuses ? $deal->status->label() : $deal->status,
            'value' => $deal->value,
            'probability' => $deal->probability,
            'close_date' => $deal->close_date?->toDateString(),
            'contact_id' => $deal->contact_id,
            'pipeline_ticket_id' => $deal->pipeline_ticket_id,
            'developer' => $deal->developer?->only(['id', 'name_en', 'name_ar']),
            'compound' => $deal->compound ? ['id' => $deal->compound->id, 'name' => $deal->compound->compound_name] : null,
            'broker' => $deal->brocker ? ['id' => $deal->brocker->id, 'name' => $deal->brocker->user?->full_name] : null,
            'inventory_unit' => $deal->inventoryUnit ? [
                'id' => $deal->inventoryUnit->id,
                'code' => $deal->inventoryUnit->code,
                'status' => $deal->inventoryUnit->status?->value,
            ] : null,
            'created_at' => $deal->created_at?->toIso8601String(),
        ];
    }

    private function detail(Deal $deal): array
    {
        return array_merge($this->summary($deal), [
            'nationality_id' => $deal->nationality_id,
            'number_of_units' => $deal->number_of_units,
            'uptown_type' => $deal->uptownType?->only(['id', 'name_en', 'name_ar']),
            'uptown' => $deal->uptown?->only(['id', 'name_en', 'name_ar']),
            'lead_id' => $deal->lead_id,
            'lister_broker' => $deal->listerBroker ? ['id' => $deal->listerBroker->id, 'name' => $deal->listerBroker->user?->full_name] : null,
            'contact' => $deal->contact ? [
                'id' => $deal->contact->id,
                'name' => $deal->contact->name,
                'phone' => $deal->contact->phone,
            ] : null,
            'commission' => $deal->commission ? [
                'id' => $deal->commission->id,
                'amount' => $deal->commission->amount,
                'percentage' => $deal->commission->percentage,
                'payout_status' => $deal->commission->payout_status?->value ?? $deal->commission->payout_status,
                'paid_at' => $deal->commission->paid_at,
            ] : null,
            'active_offer' => $deal->activeOffer ? $this->offerSummary($deal->activeOffer) : null,
            'offers' => $deal->relationLoaded('offers') ? $deal->offers->map(fn ($offer) => $this->offerSummary($offer)) : [],
            'documents' => $deal->relationLoaded('saleDocuments') ? $deal->saleDocuments->map(fn ($doc) => [
                'id' => $doc->id,
                'type' => $doc->type?->value,
                'type_label' => $doc->type?->label(),
                'title' => $doc->title,
                'issued_at' => $doc->issued_at?->toIso8601String(),
            ]) : [],
            'payment_plan' => $deal->paymentPlan,
        ]);
    }

    private function offerSummary(SaleOffer $offer): array
    {
        return [
            'id' => $offer->id,
            'status' => $offer->status?->value,
            'list_price' => $offer->list_price,
            'discount' => $offer->discount,
            'net_price' => $offer->net_price,
            'payment_method' => $offer->payment_method,
            'valid_until' => $offer->valid_until?->toIso8601String(),
            'accepted_at' => $offer->accepted_at?->toIso8601String(),
        ];
    }
}
