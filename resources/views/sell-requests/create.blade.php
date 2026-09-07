@extends('layouts.app')
@php
	$currentPage = 'sell-requests';
@endphp
@section('title', __('Create Unit Request'))
@section('content')
<div class="container">
	<h1>{{ __('Create Unit Request') }}</h1>
	<div class="mb-3">
		<a href="{{ route('sell-requests.index') }}" class="btn btn-secondary btn-sm me-1">
			<i class="fa fa-arrow-left"></i> {{ __('Back to') }} {{ __('Units Requests') }}
		</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method="POST" action="{{ route('sell-requests.store') }}" enctype="multipart/form-data" novalidate>
				@csrf
				<x-form-select name="user_id" label="{{ __('User') }}" :options="$users" required />
				<x-form-input name="age" type="number" label="{{ __('Age') }}" required />
				<div class="row">
					<div class="col-md-6">
						<x-form-input name="identity_front_image" type="file" label="{{ __('Identity front') }}" required />
					</div>
					<div class="col-md-6">
						<x-form-input name="identity_back_image" type="file" label="{{ __('Identity back') }}" required />
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<x-form-input name="country" label="{{ __('Country') }}" required />
					</div>
					<div class="col-md-4">
						<x-form-input name="city" label="{{ __('City') }}" required />
					</div>
					<div class="col-md-4">
						<x-form-input name="area" label="{{ __('Area') }}" required />
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<x-form-select name="developer_id" label="{{ __('Developer') }}" :options="$developers" />
					</div>
					<div class="col-md-6">
						<x-form-select name="compound_id" label="{{ __('Compound') }}" :options="$compounds" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<x-form-select name="uptown_type_id" label="{{ __('Unit Type') }}" :options="$uptownTypes" required />
					</div>
					<div class="col-md-6">
						<x-form-select name="unit_sub_type_id" label="{{ __('Sub Type') }}" :options="$unitSubTypes" required />
					</div>
				</div>
				<x-form-input name="price" type="number" label="{{ __('Price') }}" required :attrs="['step' => '0.01']" />
				<x-form-select
					name="installments"
					label="{{ __('Installments') }}"
					:options="[0 => __('No'), 1 => __('Yes')]"
					required
				/>
				<div class="row">
					<div class="col-md-6">
						<x-form-input name="installments_years" type="number" label="{{ __('Installment years') }}" />
					</div>
					<div class="col-md-6">
						<x-form-input name="installments_years_left" type="number" label="{{ __('Years left') }}" />
					</div>
					<div class="col-md-6">
						<x-form-input name="installments_total_price" type="number" label="{{ __('Total installment price') }}" :attrs="['step' => '0.01']" />
					</div>
					<div class="col-md-6">
						<x-form-input name="installments_price_per_year" type="number" label="{{ __('Price per year') }}" :attrs="['step' => '0.01']" />
					</div>
				</div>
				<x-form-select name="finishing" label="{{ __('Finishing') }}" :options="$finishings" required />
				<x-form-select name="execution_date" label="{{ __('Execution date') }}" :options="$executionDates" required />
				<x-form-input name="detailed_pdf" type="file" label="{{ __('Detailed PDF') }}" required />
				<x-form-input name="notes" label="{{ __('Notes') }}" />
				<button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
