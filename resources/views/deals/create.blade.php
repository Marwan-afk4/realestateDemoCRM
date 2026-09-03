@extends('layouts.app')
@php
	$currentPage = 'deals';
@endphp
@section('title', __('Create Deal'))
@section('content')
<div class="container">
	@php $fromTicket = $fromTicket ?? null; @endphp
	<h1>{{ __('Create Deal') }}</h1>
	<div class="mb-3">
		<a href="{{ route('deals.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Deals')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('deals.store') }}' class='needs-validation' novalidate>
				@csrf
				@if(!empty($fromTicket))
					<input type="hidden" name="pipeline_ticket_id" value="{{ $fromTicket->id }}">
				@endif
				<x-form-input
					name="fullname"
					type="text"
					label="{{__('Fullname')}}"
					:value="old('fullname', $fromTicket?->contact?->name)"
					required
				/>
				<x-form-input
					name="nationality_id"
					type="text"
					label="{{__('Nationality')}}"
					:value="old('nationality_id', $fromTicket?->contact?->national_id)"
				/>
				<x-form-input
					name="phone"
					type="text"
					label="{{__('Phone')}}"
					:value="old('phone', $fromTicket?->contact?->phone)"
					required
				/>
				<x-form-input
					name="email"
					type="text"
					label="{{__('Email')}}"
					:value="old('email', $fromTicket?->contact?->email)"
					required
				/>
				<x-form-select
					name="developer_id"
					type="select"
					label="{{__('Developer')}}"
					required
					:options="$developers"
					:selected="$fromTicket?->inventoryUnit?->developer_id ? (string) $fromTicket->inventoryUnit->developer_id : null"
				/>
				<x-form-select
					name="compound_id"
					type="select"
					label="{{__('Compound')}}"
					required
					:options="$compounds"
					:selected="$fromTicket?->inventoryUnit?->compound_id ? (string) $fromTicket->inventoryUnit->compound_id : null"
				/>
				<x-form-select
					name="uptown_type_id"
					type="select"
					label="{{__('Unit Type')}}"
					:options="$uptownTypes"
					:selected="$fromTicket?->contact?->uptown_type_id ? (string) $fromTicket->contact->uptown_type_id : null"
				/>
				<x-form-select
					name="uptown_id"
					label="{{ __('Listing card') }}"
					:options="$units ?? []"
					:selected="$fromTicket?->inventoryUnit?->uptown_id ? (string) $fromTicket->inventoryUnit->uptown_id : null"
				/>
				<x-form-select
					name="inventory_unit_id"
					label="{{ __('Physical unit') }}"
					:options="$inventoryUnits ?? []"
					:selected="$fromTicket?->inventory_unit_id ? (string) $fromTicket->inventory_unit_id : null"
				/>
				<x-form-select
					name="lead_id"
					label="{{ __('Lead') }}"
					:options="$leads ?? []"
					:selected="$fromTicket && $fromTicket->ticketable_type === \App\Models\Lead::class ? (string) $fromTicket->ticketable_id : null"
				/>
				<x-form-select
					name="brocker_id"
					label="{{ __('Broker (closer)') }}"
					:options="$brokers ?? []"
					:selected="$fromTicket?->brocker_id ? (string) $fromTicket->brocker_id : null"
				/>
				<x-form-select
					name="lister_broker_id"
					label="{{ __('Lister broker') }}"
					:options="['' => __('Auto-detect from pipeline')] + ($brokers ?? [])"
				/>
				<x-form-input
					name="value"
					type="number"
					label="{{ __('Deal value') }}"
				/>
				<x-form-input
					name="close_date"
					type="date"
					label="{{ __('Close date') }}"
				/>
				<x-form-input
					name="probability"
					type="number"
					label="{{ __('Probability %') }}"
				/>
				<x-form-input
					name="number_of_units"
					type="text"
					label="{{__('Number Of Units')}}"
					required
				/>
				<x-form-select
					name="status"
					type="select"
					label="{{__('Status')}}"
					required
                    :options="$statuses"
				/>
				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Add') }}</button>
			</form>
		</div>
	</div>
</div>@endsection
