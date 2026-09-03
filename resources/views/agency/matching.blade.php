@extends('layouts.app')
@php $currentPage = 'agency-matching'; @endphp
@section('title', __('Unit matching'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="mb-2">{{ __('Unit matching') }}</h2>
        <p class="text-body-tertiary mb-0">{{ __('Match live inventory to contact budget, area, and unit type.') }}</p>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">{{ __('Contact') }}</label>
                    <select name="contact_id" class="form-select" required onchange="this.form.submit()">
                        <option value="">—</option>
                        @foreach($contacts as $id => $name)
                            <option value="{{ $id }}" @selected(request('contact_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" type="submit">{{ __('Match units') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if($contact)
        <div class="alert alert-outline-info mb-4">
            {{ __('Budget') }}: {{ $contact->budget_min ? number_format((float)$contact->budget_min) : '—' }} – {{ $contact->budget_max ? number_format((float)$contact->budget_max) : '—' }}
            · {{ __('Area') }}: {{ $contact->preferred_area ?: '—' }}
            · {{ __('Type') }}: {{ $contact->uptownType?->name_en ?? '—' }}
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover crm-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">{{ __('Unit') }}</th>
                        <th>{{ __('Compound') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Score') }}</th>
                        <th class="pe-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($matches as $row)
                        @php $unit = $row['unit']; @endphp
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('inventory-units.show', $unit) }}">{{ $unit->code }}</a>
                                <div class="fs-9 text-body-tertiary">{{ $unit->address() }}</div>
                            </td>
                            <td>{{ $unit->compound?->compound_name ?? '—' }}</td>
                            <td>{{ number_format($row['price']) }}</td>
                            <td><span class="badge badge-phoenix badge-phoenix-primary">{{ $row['score'] }}</span></td>
                            <td class="pe-4 text-end">
                                <span class="badge badge-phoenix {{ $unit->status->phoenixBadge() }}">{{ $unit->status->label() }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6"><div class="crm-empty">{{ $contact ? __('No matching units.') : __('Select a contact.') }}</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
