@extends('layouts.app')
@php $currentPage = 'pipeline'; @endphp
@section('title', __('Pipeline'))
@section('content')
@include('crm.styles')
@php $ticketCount = $tickets->sum(fn ($column) => $column->count()); @endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Pipeline') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Move tickets through the sale. Lost always needs a reason.') }}</p>
        </div>
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <div class="search-box">
                <input type="search" name="keyword" class="form-control search-input" placeholder="{{ __('Search contacts') }}" value="{{ request('keyword') }}">
                <span class="fas fa-search search-box-icon"></span>
            </div>
            <select name="type" class="form-select form-select-sm" style="width: auto; min-width: 9rem;" onchange="this.form.submit()">
                <option value="">{{ __('All types') }}</option>
                @foreach($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @if(request('keyword') || request('type'))
                <a href="{{ route('pipeline.index') }}" class="btn btn-sm btn-phoenix-secondary">{{ __('Reset') }}</a>
            @endif
            <span class="badge badge-phoenix badge-phoenix-primary">{{ trans_choice(':count ticket|:count tickets', $ticketCount, ['count' => $ticketCount]) }}</span>
        </form>
    </div>

    <div class="crm-board scrollbar">
        @foreach($stages as $stage)
            @php $column = $tickets->get($stage->value) ?? collect(); @endphp
            <div class="crm-board-col">
                <div class="crm-board-head">
                    <div class="crm-board-head-inner {{ $stage->phoenixBorder() }}">
                        <h5 class="crm-board-title">{{ $stage->label() }}</h5>
                        <span class="crm-board-count">{{ $column->count() }}</span>
                    </div>
                </div>
                <div class="crm-board-body scrollbar">
                    @forelse ($column as $ticket)
                        @php $contact = $ticket->contact; @endphp
                        <div class="crm-ticket hover-actions-trigger">
                            <div class="d-flex gap-2 mb-2">
                                <x-crm-avatar :contact="$contact" size="m" />
                                <div class="min-w-0 flex-1">
                                    <a class="crm-ticket-name d-block text-truncate" href="{{ route('contacts.show', $ticket->contact_id) }}">{{ $contact?->name ?? __('Untitled') }}</a>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        <span class="badge {{ $ticket->type->badgeClass() }} fs-10">{{ $ticket->type->label() }}</span>
                                        @if($ticket->isLocked())
                                            <span class="badge badge-phoenix badge-phoenix-secondary fs-10">{{ __('Locked') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <p class="mb-2 fs-9 text-body-tertiary text-truncate">
                                <span class="uil uil-user me-1"></span>{{ $ticket->owner?->full_name ?? $ticket->brocker?->user?->full_name ?? __('Unassigned') }}
                                @if($contact?->preferred_area)
                                    <span class="mx-1">·</span>{{ $contact->preferred_area }}
                                @endif
                            </p>
                            <div class="d-flex align-items-center gap-1 mb-2">
                                @if($contact?->telLink())
                                    <a class="crm-action-icon" href="{{ $contact->telLink() }}" title="{{ __('Call') }}"><span class="uil uil-phone"></span></a>
                                @endif
                                @if($contact?->whatsappLink())
                                    <a class="crm-action-icon text-success" target="_blank" href="{{ $contact->whatsappLink() }}" title="{{ __('WhatsApp') }}"><span class="uil uil-whatsapp"></span></a>
                                @endif
                                <a class="crm-action-icon ms-auto" href="{{ route('contacts.show', $ticket->contact_id) }}" title="{{ __('Open') }}"><span class="uil uil-arrow-up-right"></span></a>
                            </div>

                            <form method="POST" action="{{ route('pipeline.stage', $ticket) }}">
                                @csrf
                                <select name="stage" class="form-select form-select-sm" onchange="if (this.value === 'lost') { this.form.querySelector('.lost-fields').classList.remove('d-none'); } else { this.form.submit(); }">
                                    @foreach($stages as $option)
                                        <option value="{{ $option->value }}" @selected($ticket->stage === $option)>{{ $option->label() }}</option>
                                    @endforeach
                                </select>
                                <div class="lost-fields d-none mt-2">
                                    <select name="lost_reason" class="form-select form-select-sm mb-1">
                                        @foreach($lostReasons as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="lost_note" class="form-control form-control-sm mb-1" placeholder="{{ __('Note') }}">
                                    <button class="btn btn-sm btn-danger w-100">{{ __('Mark lost') }}</button>
                                </div>
                            </form>

                            <details class="mt-2">
                                <summary class="fs-10 fw-bold text-body-tertiary" style="cursor:pointer;list-style:none;">{{ __('Assign / transfer') }}</summary>
                                <form method="POST" action="{{ route('pipeline.assign', $ticket) }}" class="mt-2">
                                    @csrf
                                    <select name="brocker_id" class="form-select form-select-sm mb-1" required>
                                        <option value="">{{ __('Choose broker') }}</option>
                                        @foreach($brokers as $broker)
                                            <option value="{{ $broker->id }}" @selected($ticket->brocker_id == $broker->id)>{{ $broker->user?->full_name ?? $broker->id }}</option>
                                        @endforeach
                                    </select>
                                    <input type="date" name="expires_at" class="form-control form-control-sm mb-1">
                                    <button class="btn btn-sm btn-phoenix-primary w-100">{{ __('Save assignment') }}</button>
                                </form>
                            </details>
                            @include('pipeline._assign-unit', ['ticket' => $ticket, 'inventoryUnits' => $inventoryUnits])
                        </div>
                    @empty
                        <div class="crm-empty">{{ __('No tickets in this stage') }}</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
