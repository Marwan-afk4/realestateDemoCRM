@extends('layouts.app')
@php $currentPage = 'agency-workspace'; @endphp
@section('title', __('Agency workspace'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ $agency->name }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Marketing agency desk — agents, leads, and unit matching.') }}</p>
        </div>
        @can('view-unit-matching')
            <a href="{{ route('agency.matching') }}" class="btn btn-primary">{{ __('Unit matching') }}</a>
        @endcan
    </div>

    <div class="row g-3 mb-4">
        @foreach ([__('Agents') => $stats['agents'], __('Leads') => $stats['leads'], __('Open pipeline') => $stats['open_tickets']] as $label => $value)
            <div class="col-md-4">
                <div class="card"><div class="card-body"><div class="fs-10 text-body-tertiary">{{ $label }}</div><div class="fs-3 fw-bold">{{ $value }}</div></div></div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Recent leads') }}</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover crm-table mb-0">
                        <thead><tr><th class="ps-4">{{ __('Lead') }}</th><th>{{ __('Broker') }}</th><th class="pe-4">{{ __('Stage') }}</th></tr></thead>
                        <tbody>
                            @forelse ($recentLeads as $lead)
                                <tr>
                                    <td class="ps-4"><a href="{{ route('leads.show', $lead) }}">{{ $lead->lead_name }}</a></td>
                                    <td>{{ $lead->brocker?->user?->full_name ?? '—' }}</td>
                                    <td class="pe-4">{!! $lead->ticket?->stage?->badge() ?? '—' !!}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-5"><div class="crm-empty">{{ __('No leads yet.') }}</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Agency agents') }}</h5></div>
                <div class="card-body">
                    @forelse ($agents as $agent)
                        <div class="d-flex justify-content-between py-2 border-bottom border-translucent">
                            <span>{{ $agent->full_name }}</span>
                            <span class="fs-9 text-body-tertiary">{{ $agent->phone }}</span>
                        </div>
                    @empty
                        <div class="crm-empty">{{ __('No portal agents yet.') }}</div>
                    @endforelse
                </div>
            </div>
            @canany(['view-marketing-agencies', 'view-agency-workspace'])
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">{{ __('Add agent login') }}</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('marketing-agencies.agents.store', $agency) }}">
                            @csrf
                            <x-form-input name="first_name" label="{{ __('First name') }}" required />
                            <x-form-input name="last_name" label="{{ __('Last name') }}" required />
                            <x-form-input name="phone" label="{{ __('Phone') }}" required />
                            <x-form-input name="email" type="email" label="{{ __('Email') }}" />
                            <x-form-input name="password" type="password" label="{{ __('Password') }}" required />
                            <button class="btn btn-primary w-100" type="submit">{{ __('Create agent') }}</button>
                        </form>
                    </div>
                </div>
            @endcanany
        </div>
    </div>
</div>
@endsection
