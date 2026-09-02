@extends('layouts.app')
@php
	$currentPage = 'leads';
@endphp
@section('title', $lead->lead_name)
@section('content')
<div class="container-fluid">
	<h1>{{ $lead->lead_name }}</h1>
	<div class="mb-3">
		<a href="{{ route('leads.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Leads')}}</a>
		<a href='{{ route('leads.edit', $lead) }}' class="btn btn-warning btn-sm me-1">{{ __('Edit') }} <i class="fa fa-edit"></i></a>
	</div>
	<div class="card mb-4">
		<div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">{{ __('Lead Information') }}</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>{{ __("Id") }}:</strong> {{ $lead->id }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Lead Name") }}:</strong> {{ $lead->lead_name }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Lead Phone") }}:</strong> {{ $lead->lead_phone }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Status") }}:</strong> {!! $lead->status->badge() !!}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Interested Place") }}:</strong> {{ $lead->interested_place ?? '-' }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Created At") }}:</strong> {{ $lead->created_at->diffForHumans() }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Updated At") }}:</strong> {{ $lead->updated_at->diffForHumans() }}
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="text-primary mb-3">{{ __('Related Information') }}</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>{{ __("Marketing Agency") }}:</strong> {{ $lead->marketing_agency->name ?? '-' }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Uptown") }}:</strong> 
                            @if($lead->uptown)
                                <a href="{{ route('uptowns.show', $lead->uptown) }}">{{ $lead->uptown->name }}</a>
                            @else
                                -
                            @endif
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Current Broker") }}:</strong> 
                            @if($lead->brocker)
                                <a href="{{ route('brockers.show', $lead->brocker) }}">{{ $lead->brocker->user->full_name ?? $lead->brocker->name ?? $lead->brocker->id }}</a>
                            @else
                                <span class="text-muted">{{ __('None') }}</span>
                            @endif
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Sales Man Name") }}:</strong> {{ $lead->sales_man_name ?? '-' }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __("Sales Man Phone") }}:</strong> {{ $lead->sales_man_phone ?? '-' }}
                        </li>
                    </ul>
                </div>
            </div>
		</div>
	</div>

    <div class="mt-4">
        <livewire:lead-assign-broker :lead="$lead" />
    </div>
</div>
@endsection