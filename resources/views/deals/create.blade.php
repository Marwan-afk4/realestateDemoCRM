@extends('layouts.app')
@php
	$currentPage = 'deals';
@endphp
@section('title', __('Create Deal'))
@section('content')
<div class="container">
	<h1>{{ __('Create Deal') }}</h1>
	<div class="mb-3">
		<a href="{{ route('deals.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Deals')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('deals.store') }}' class='needs-validation' novalidate>
				@csrf
				<x-form-input
					name="fullname"
					type="text"
					label="{{__('Fullname')}}"
					required
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
					required
				/>
				<x-form-input
					name="email"
					type="text"
					label="{{__('Email')}}"
					required
				/>
				<x-form-select
					name="developer_id"
					type="select"
					label="{{__('Developer')}}"
					required
					:options="$developers"
				/>
				<x-form-select
					name="compound_id"
					type="select"
					label="{{__('Compound')}}"
					required
					:options="$compounds"
				/>
				<x-form-select
					name="uptown_type_id"
					type="select"
					label="{{__('Unit Type')}}"
					:options="$uptownTypes"
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
