@extends('layouts.app')
@php $currentPage = 'inventory-units'; @endphp
@section('title', $unit->code)
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ $unit->code }}</h2>
            <p class="text-body-tertiary mb-0">{{ $unit->address() }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory-units.index') }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
            <a href="{{ route('inventory-units.edit', $unit) }}" class="btn btn-phoenix-secondary">{{ __('Edit') }}</a>
            @if($unit->status->blocksOtherSale() && $unit->status !== \App\Enums\InventoryStatus::Sold && $unit->status !== \App\Enums\InventoryStatus::HandedOver)
                <form method="POST" action="{{ route('inventory-units.release', $unit) }}">
                    @csrf
                    <button class="btn btn-phoenix-danger" type="submit">{{ __('Release hold') }}</button>
                </form>
            @endif
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Stock') }}</h5>
                    <p><span class="badge badge-phoenix {{ $unit->status->phoenixBadge() }}">{{ $unit->status->label() }}</span></p>
                    <p class="mb-1">{{ __('List price') }}: {{ $unit->list_price ? number_format((float) $unit->list_price) : '—' }}</p>
                    <p class="mb-1">{{ __('Current price') }}: {{ $unit->current_price ? number_format((float) $unit->current_price) : '—' }}</p>
                    <p class="mb-1">{{ __('Hold until') }}: {{ $unit->reserved_until?->format('Y-m-d H:i') ?? '—' }}</p>
                    @if($unit->uptown)
                        <p class="mb-0">{{ __('Listing') }}: <a href="{{ route('uptowns.show', $unit->uptown) }}">{{ $unit->uptown->name }}</a></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Holds') }}</h5>
                    @forelse ($unit->holds as $hold)
                        <div class="d-flex justify-content-between border-top border-translucent py-2">
                            <div>
                                <strong>{{ $hold->type->label() }}</strong>
                                <div class="fs-9 text-body-tertiary">{{ __('Expires') }} {{ $hold->expires_at->format('Y-m-d H:i') }}
                                    @if($hold->released_at) · {{ __('Released') }} {{ $hold->release_reason }} @endif
                                </div>
                            </div>
                            @if($hold->deal)
                                <a href="{{ route('deals.show', $hold->deal) }}">{{ __('Deal') }} #{{ $hold->deal_id }}</a>
                            @endif
                        </div>
                    @empty
                        <div class="crm-empty">{{ __('No holds.') }}</div>
                    @endforelse
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Pipeline interest') }}</h5>
                    @forelse ($unit->pipelineTickets as $ticket)
                        <div class="d-flex justify-content-between border-top border-translucent py-2">
                            <div>
                                <a href="{{ route('contacts.show', $ticket->contact_id) }}">{{ $ticket->contact?->name ?? __('Untitled') }}</a>
                                <div class="fs-9 text-body-tertiary">{{ $ticket->type->label() }} · {{ $ticket->stage->label() }}</div>
                            </div>
                            <a href="{{ route('pipeline.index') }}">{{ __('Board') }}</a>
                        </div>
                    @empty
                        <div class="crm-empty">{{ __('No pipeline tickets on this unit.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
