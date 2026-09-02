@extends('layouts.app')
@php
	$currentPage = 'deals';
@endphp
@section('title', __('Edit Deal'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Deal') }}</h1>
	<div class="mb-3">
		<a href="{{ route('deals.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Deals')}}</a>
		<a href='{{ route('deals.show', $deal) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('deals.update', $deal->id) }}' class='needs-validation' novalidate>
				@csrf
				@method('PUT')
				<x-form-input
					name="fullname"
					type="text"
					label="{{__('Fullname')}}"
					:value="$deal->fullname ?? ''"

				/>
				<x-form-input
					name="nationality_id"
					type="text"
					label="{{__('Nationality')}}"
				/>
				<x-form-input
					name="phone"
					type="text"
					label="{{__('Phone')}}"
					:value="$deal->phone ?? ''"

				/>
				<x-form-input
					name="email"
					type="text"
					label="{{__('Email')}}"
					:value="$deal->email ?? ''"

				/>
				<x-form-select
					name="developer_id"
					type="select"
					label="{{__('Developer')}}"
					:selected="$deal->developer_id ?? ''"
					:options="$developers"
				/>
				<x-form-select
					name="compound_id"
					type="select"
					label="{{__('Compound')}}"
					:selected="$deal->compound_id ?? ''"
					:options="$compounds"
				/>
				<x-form-select
					name="uptown_type_id"
					type="select"
					label="{{__('Unit Type')}}"
					:selected="$deal->uptown_type_id ?? ''"
					:options="$uptownTypes"
				/>
				<x-form-select
					name="uptown_id"
					label="{{ __('Listing card') }}"
					:selected="$deal->uptown_id ?? ''"
					:options="$units ?? []"
				/>
				<x-form-select
					name="inventory_unit_id"
					label="{{ __('Physical unit') }}"
					:selected="$deal->inventory_unit_id ?? ''"
					:options="$inventoryUnits ?? []"
				/>
				<x-form-select
					name="lead_id"
					label="{{ __('Lead') }}"
					:selected="$deal->lead_id ?? ''"
					:options="$leads ?? []"
				/>
				<x-form-select
					name="brocker_id"
					label="{{ __('Broker') }}"
					:selected="$deal->brocker_id ?? ''"
					:options="$brokers ?? []"
				/>
				<x-form-input
					name="value"
					type="number"
					label="{{ __('Deal value') }}"
					:value="$deal->value ?? ''"
				/>
				<x-form-input
					name="close_date"
					type="date"
					label="{{ __('Close date') }}"
					:value="optional($deal->close_date)->format('Y-m-d')"
				/>
				<x-form-input
					name="probability"
					type="number"
					label="{{ __('Probability %') }}"
					:value="$deal->probability ?? ''"
				/>
				<x-form-input
					name="number_of_units"
					type="text"
					label="{{__('Number Of Units')}}"
					:value="$deal->number_of_units ?? ''"
				/>
				<x-form-select
					name="status"
					type="select"
					label="{{__('Status')}}"
                    :selected="$deal->status->value ?? ''"
                    :options="$statuses"
				/>
				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
