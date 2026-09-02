@extends('layouts.app')
@php
	$currentPage = 'leads';
@endphp
@section('title', __('Edit Lead'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Lead') }}</h1>
	<div class="mb-3">
		<a href="{{ route('leads.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Leads')}}</a>
		<a href='{{ route('leads.show', $lead) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
		{{-- <a href="{{ route('leads.deals', $lead) }}" class="list-group-item">{{ __('deal') }}</a> --}}
		{{-- <a href="{{ route('leads.broker_leads', $lead) }}" class="list-group-item">{{ __('brokerlead') }}</a> --}}
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('leads.update', $lead->id) }}' class='needs-validation' novalidate>
				@csrf
				@method('PUT')
				<x-form-input 
					name="lead_name"
					type="text"
					label="{{__('Lead Name')}}"
					:value="$lead->lead_name ?? ''"
				/>
				<x-form-input 
					name="lead_phone"
					type="text"
					label="{{__('Lead Phone')}}"
					:value="$lead->lead_phone ?? ''"
				/>
				<x-form-input 
					name="interested_place"
					type="text"
					label="{{__('Interested Place')}}"
					:value="$lead->interested_place ?? ''"
				/>
				<x-form-select 
					name="uptown_id"
					type="select"
					label="{{__('Uptown')}}"
					:selected="$lead->uptown_id ?? ''"
					:options="$uptowns"
				/>
				<x-form-select 
					name="marketing_agency_id"
					type="select"
					label="{{__('Marketing Agency')}}"
					:selected="$lead->marketing_agency_id ?? ''"
					:options="$marketing_agencies"
				/>
				<x-form-input 
					name="sales_man_name"
					type="text"
					label="{{__('Sales Man Name')}}"
					:value="$lead->sales_man_name ?? ''"
				/>
				<x-form-input 
					name="sales_man_phone"
					type="text"
					label="{{__('Sales Man Phone')}}"
					:value="$lead->sales_man_phone ?? ''"
				/>
				<x-form-select 
					name="status"
					type="select"
					label="{{__('Status')}}"
					:selected="$lead->status->value ?? ''"
					:options="$leadStatuses"
				/>
				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection