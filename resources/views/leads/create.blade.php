@extends('layouts.app')
@php
	$currentPage = 'leads';
@endphp
@section('title', __('Create Lead'))
@section('content')
<div class="container">
	<h1>{{ __('Create Lead') }}</h1>
	<div class="mb-3">
		<a href="{{ route('leads.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Leads')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('leads.store') }}' class='needs-validation' novalidate>
				@csrf
				<x-form-input 
					name="lead_name"
					type="text"
					label="{{__('Lead Name')}}"
					required
				/>
				<x-form-input 
					name="lead_phone"
					type="text"
					label="{{__('Lead Phone')}}"
					required
				/>
				<x-form-input 
					name="interested_place"
					type="text"
					label="{{__('Interested Place')}}"
				/>
				<x-form-select 
					name="uptown_id"
					type="select"
					label="{{__('Uptown')}}"
					:options="$uptowns"
				/>
				<x-form-select 
					name="marketing_agency_id"
					type="select"
					label="{{__('Marketing Agency')}}"
					:options="$marketing_agencies"
				/>
				<x-form-input 
					name="sales_man_name"
					type="text"
					label="{{__('Sales Man Name')}}"
				/>
				<x-form-input 
					name="sales_man_phone"
					type="text"
					label="{{__('Sales Man Phone')}}"
				/>
				<x-form-select 
					name="status"
					type="select"
					label="{{__('Status')}}"
					:options="$leadStatuses"
				/>
				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Add') }}</button>
			</form>
		</div>
	</div>
</div>@endsection