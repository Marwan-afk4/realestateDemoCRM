@extends('layouts.app')
@php $currentPage = 'after-sales'; @endphp
@section('title', __('Ticket') . ' #' . $ticket->id)
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <div>
            <h2 class="mb-1">#{{ $ticket->id }} — {{ $ticket->type->label() }}</h2>
            <span class="badge badge-phoenix badge-phoenix-secondary">{{ $ticket->status->label() }}</span>
        </div>
        <a href="{{ route('after-sales.index') }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Details') }}</h5></div>
                <div class="card-body">
                    <p class="fw-semibold">{{ $ticket->title }}</p>
                    @if($ticket->description)<p class="text-body-secondary">{{ $ticket->description }}</p>@endif
                    <dl class="row mb-0">
                        <dt class="col-sm-3">{{ __('Contact') }}</dt>
                        <dd class="col-sm-9">@if($ticket->contact)<a href="{{ route('contacts.show', $ticket->contact) }}">{{ $ticket->contact->name }}</a>@else — @endif</dd>
                        <dt class="col-sm-3">{{ __('Deal') }}</dt>
                        <dd class="col-sm-9">@if($ticket->deal)<a href="{{ route('deals.show', $ticket->deal) }}">#{{ $ticket->deal_id }}</a>@else — @endif</dd>
                        <dt class="col-sm-3">{{ __('Unit') }}</dt>
                        <dd class="col-sm-9">{{ $ticket->inventoryUnit?->code ?? '—' }}</dd>
                        <dt class="col-sm-3">{{ __('Assignee') }}</dt>
                        <dd class="col-sm-9">{{ $ticket->assignee?->full_name ?? '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Update status') }}</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('after-sales.status', $ticket) }}">
                        @csrf
                        <select name="status" class="form-select mb-3" required>
                            @foreach(\App\Enums\AfterSalesTicketStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($ticket->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary w-100" type="submit">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
