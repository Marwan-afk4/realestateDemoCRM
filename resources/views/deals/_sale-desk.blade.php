@include('crm.styles')
@php
    $unit = $deal->inventoryUnit;
    $offer = $deal->activeOffer;
    $plan = $deal->paymentPlan;
@endphp
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="mb-3">{{ __('Physical unit') }}</h5>
                @if($unit)
                    <p class="mb-1"><a href="{{ route('inventory-units.show', $unit) }}">{{ $unit->code }}</a></p>
                    <p class="mb-1">{{ $unit->address() }}</p>
                    <p class="mb-3"><span class="badge badge-phoenix {{ $unit->status->phoenixBadge() }}">{{ $unit->status->label() }}</span>
                        @if($unit->reserved_until) <span class="fs-9 text-body-tertiary">{{ __('until') }} {{ $unit->reserved_until->format('Y-m-d H:i') }}</span> @endif
                    </p>
                    @if($unit->status !== \App\Enums\InventoryStatus::Sold && $unit->status !== \App\Enums\InventoryStatus::HandedOver)
                        <form method="POST" action="{{ route('deals.hold', $deal) }}" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-5">
                                <select name="type" class="form-select form-select-sm" required>
                                    @foreach(\App\Enums\HoldType::labels() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="datetime-local" name="expires_at" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-phoenix-warning w-100">{{ __('Hold') }}</button>
                            </div>
                        </form>
                    @endif
                    @if($unit->status === \App\Enums\InventoryStatus::Sold)
                        <form method="POST" action="{{ route('deals.handover', $deal) }}" class="mt-2">
                            @csrf
                            <button class="btn btn-sm btn-primary">{{ __('Mark handed over') }}</button>
                        </form>
                    @endif
                @else
                    <div class="crm-empty">{{ __('Attach a physical unit on the deal edit form. Approving without one is blocked.') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="mb-3">{{ __('Offer') }}</h5>
                @if($offer)
                    <p class="mb-1">{{ __('Net') }}: <strong>{{ number_format((float) $offer->net_price) }}</strong>
                        <span class="badge badge-phoenix {{ $offer->status->phoenixBadge() }}">{{ $offer->status->label() }}</span>
                    </p>
                    <p class="fs-9 text-body-tertiary">{{ __('List') }} {{ number_format((float) $offer->list_price) }} · {{ __('Discount') }} {{ number_format((float) $offer->discount) }} · {{ \App\Models\SaleOffer::paymentMethods()[$offer->payment_method] ?? $offer->payment_method }}</p>
                    @if($offer->status !== \App\Enums\SaleOfferStatus::Accepted)
                        <form method="POST" action="{{ route('deals.offers.accept', [$deal, $offer]) }}">
                            @csrf
                            <button class="btn btn-sm btn-success">{{ __('Accept offer') }}</button>
                        </form>
                    @endif
                @endif
                <form method="POST" action="{{ route('deals.offers.store', $deal) }}" class="mt-3">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-4"><input class="form-control form-control-sm" name="list_price" type="number" step="0.01" placeholder="{{ __('List price') }}" value="{{ $deal->inventoryUnit?->price() ?? $deal->value }}" required></div>
                        <div class="col-md-4"><input class="form-control form-control-sm" name="discount" type="number" step="0.01" placeholder="{{ __('Discount') }}" value="0"></div>
                        <div class="col-md-4">
                            <select name="payment_method" class="form-select form-select-sm">
                                @foreach(\App\Models\SaleOffer::paymentMethods() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4"><input class="form-control form-control-sm" name="down_payment_percent" type="number" placeholder="{{ __('Down %') }}" value="20"></div>
                        <div class="col-md-4"><input class="form-control form-control-sm" name="installment_count" type="number" placeholder="{{ __('Installments') }}" value="12"></div>
                        <div class="col-md-4"><button class="btn btn-sm btn-primary w-100">{{ __('Issue offer') }}</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="mb-0">{{ __('Sale documents') }}</h5>
                    <form method="POST" action="{{ route('deals.documents.store', $deal) }}" class="d-flex gap-1">
                        @csrf
                        <select name="type" class="form-select form-select-sm">
                            @foreach(\App\Enums\SaleDocumentType::labels() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-phoenix-secondary">{{ __('Issue') }}</button>
                    </form>
                </div>
                @forelse ($deal->saleDocuments as $document)
                    <a href="{{ route('sale-documents.show', $document) }}" class="d-flex justify-content-between py-2 border-top border-translucent text-body text-decoration-none">
                        <span>{{ $document->type->label() }} · {{ $document->issued_at?->format('Y-m-d') }}</span>
                        <span class="uil uil-angle-right"></span>
                    </a>
                @empty
                    <div class="crm-empty">{{ __('No sale documents yet. App Contracts are T&C, not these.') }}</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="mb-0">{{ __('Buyer payment plan') }}</h5>
                    @unless($plan)
                        <form method="POST" action="{{ route('deals.payment-plan.store', $deal) }}">
                            @csrf
                            <button class="btn btn-sm btn-phoenix-secondary">{{ __('Create plan') }}</button>
                        </form>
                    @endunless
                </div>
                @if($plan)
                    <p class="fs-9">{{ __('Total') }} {{ number_format((float) $plan->total_price) }} · {{ __('Paid') }} {{ number_format($plan->paidTotal()) }} · {{ __('Remaining') }} {{ number_format($plan->remaining()) }}</p>
                    @foreach($plan->installments as $row)
                        <div class="d-flex justify-content-between align-items-center py-2 border-top border-translucent gap-2">
                            <div>
                                <div class="fw-semibold">{{ $row->label }}</div>
                                <div class="fs-10 text-body-tertiary">{{ $row->due_date->format('Y-m-d') }} · {{ number_format((float) $row->amount) }}</div>
                            </div>
                            <span class="badge badge-phoenix {{ $row->status->phoenixBadge() }}">{{ $row->status->label() }}</span>
                            @if($row->remaining() > 0)
                                <form method="POST" action="{{ route('deals.receipts.store', [$deal, $row]) }}" class="d-flex gap-1">
                                    @csrf
                                    <input type="number" step="0.01" name="amount" class="form-control form-control-sm" style="width:6rem" value="{{ $row->remaining() }}">
                                    <button class="btn btn-sm btn-primary">{{ __('Pay') }}</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="crm-empty">{{ __('Create a plan after an offer, or on approve.') }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">{{ __('Commission payout') }}</h5>
                @if($deal->commission)
                    <p class="mb-2">
                        {{ __('Closed price') }} {{ number_format((float) ($deal->commission->closed_unit_price ?? $deal->value)) }}
                        · {{ $deal->commission->percentage }}%
                        · <strong>{{ number_format((float) $deal->commission->amount) }}</strong>
                        <span class="badge badge-phoenix {{ $deal->commission->payout_status?->phoenixBadge() ?? 'badge-phoenix-secondary' }}">{{ $deal->commission->payout_status?->label() ?? '—' }}</span>
                    </p>
                    @foreach($deal->commission->splits as $split)
                        <div class="fs-9">{{ $split->role->label() }}: {{ $split->user?->full_name ?? '—' }} · {{ $split->percentage }}% · {{ number_format((float) $split->amount) }}</div>
                    @endforeach
                    <form method="POST" action="{{ route('deals.payout', $deal) }}" class="d-flex gap-2 mt-3">
                        @csrf
                        <select name="payout_status" class="form-select form-select-sm" style="width:12rem">
                            @foreach(\App\Enums\CommissionPayoutStatus::labels() as $value => $label)
                                <option value="{{ $value }}" @selected($deal->commission->payout_status?->value === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-primary">{{ __('Update payout') }}</button>
                    </form>
                @else
                    <div class="crm-empty">{{ __('Commission is snapshotted when the deal is approved on a sold unit.') }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
