@extends('layouts.app')
@php $currentPage = 'marketing-agencies'; @endphp
@section('title', $agency->name)
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-1">{{ $agency->name }}</h2>
            <p class="text-body-tertiary mb-0">{{ $agency->phone }} @if($agency->email)· {{ $agency->email }}@endif</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('agency.workspace', ['agency_id' => $agency->id]) }}" class="btn btn-primary">{{ __('Open workspace') }}</a>
            <a href="{{ route('marketing-agencies.edit', $agency) }}" class="btn btn-phoenix-secondary">{{ __('Edit') }}</a>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Recent leads') }}</h5></div>
                <div class="card-body p-0">
                    <table class="table table-hover crm-table mb-0">
                        <tbody>
                            @forelse ($agency->leads->take(15) as $lead)
                                <tr>
                                    <td class="ps-4"><a href="{{ route('leads.show', $lead) }}">{{ $lead->lead_name }}</a></td>
                                    <td class="pe-4">{!! $lead->ticket?->stage?->badge() ?? '—' !!}</td>
                                </tr>
                            @empty
                                <tr><td class="py-5"><div class="crm-empty">{{ __('No leads.') }}</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">{{ __('Portal agents') }}</h5></div>
                <div class="card-body">
                    @forelse ($agency->agents as $agent)
                        <div class="py-2 border-bottom border-translucent">{{ $agent->full_name }} · {{ $agent->phone }}</div>
                    @empty
                        <div class="crm-empty">{{ __('No agents yet.') }}</div>
                    @endforelse
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5 class="mb-0">{{ __('Add agency login') }}</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('marketing-agencies.agents.store', $agency) }}">
                        @csrf
                        <x-form-input name="first_name" label="{{ __('First name') }}" required />
                        <x-form-input name="last_name" label="{{ __('Last name') }}" required />
                        <x-form-input name="phone" label="{{ __('Phone') }}" required />
                        <x-form-input name="email" type="email" label="{{ __('Email') }}" />
                        <x-form-input name="password" type="password" label="{{ __('Password') }}" required />
                        <button class="btn btn-primary w-100" type="submit">{{ __('Create login') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
