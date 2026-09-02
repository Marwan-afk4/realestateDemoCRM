@extends('layouts.app')
@php $currentPage = 'inventory-units'; @endphp
@section('title', __('Inventory'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Inventory') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('One row is one physical unit: project, phase, building, floor, number.') }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form method="GET" class="d-flex gap-2">
                <div class="search-box">
                    <input type="search" name="keyword" class="form-control search-input" placeholder="{{ __('Code, building, number') }}" value="{{ request('keyword') }}">
                    <span class="fas fa-search search-box-icon"></span>
                </div>
                <select name="status" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('inventory-units.create') }}" class="btn btn-primary"><span class="fas fa-plus me-2"></span>{{ __('Add unit') }}</a>
        </div>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover crm-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">{{ __('Unit') }}</th>
                        <th>{{ __('Project') }}</th>
                        <th>{{ __('Address') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Hold until') }}</th>
                        <th>{{ __('Deal') }}</th>
                        <th class="pe-4">{{ __('Price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($units as $unit)
                        <tr>
                            <td class="ps-4">
                                <a class="fw-bold" href="{{ route('inventory-units.show', $unit) }}">{{ $unit->code }}</a>
                                <div class="fs-10 text-body-tertiary">{{ $unit->unit_number }}</div>
                            </td>
                            <td>{{ $unit->compound?->compound_name ?? '—' }}</td>
                            <td class="fs-9">{{ collect([$unit->phase, $unit->building, $unit->floor])->filter()->implode(' / ') ?: '—' }}</td>
                            <td><span class="badge badge-phoenix {{ $unit->status->phoenixBadge() }}">{{ $unit->status->label() }}</span></td>
                            <td class="fs-9">{{ $unit->reserved_until?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>
                                @if($unit->activeDeal)
                                    <a href="{{ route('deals.show', $unit->activeDeal) }}">#{{ $unit->activeDeal->id }}</a>
                                    <div class="fs-10 text-body-tertiary">{{ $unit->activeDeal->contact?->name ?? $unit->activeDeal->fullname }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="pe-4">{{ $unit->price() ? number_format($unit->price()) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-5"><div class="crm-empty">{{ __('No physical units yet.') }}</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($units->hasPages())
            <div class="card-footer">{{ $units->links() }}</div>
        @endif
    </div>
</div>
@endsection
