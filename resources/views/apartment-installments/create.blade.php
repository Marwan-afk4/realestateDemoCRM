@extends('layouts.app')
@php
	$currentPage = 'apartment-installments';
@endphp
@section('title', __('Create Mortgage Request'))
@section('content')
<div class="container">
	<h1>{{ __('Create Mortgage Request') }}</h1>
	<div class="mb-3">
		<a href="{{ route('apartment-installments.index') }}" class="btn btn-secondary btn-sm me-1">
			<i class="fa fa-arrow-left"></i> {{ __('Back to') }} {{ __('Mortgage Requests') }}
		</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method="POST" action="{{ route('apartment-installments.store') }}" enctype="multipart/form-data" novalidate>
				@csrf
				<x-form-select name="user_id" label="{{ __('User') }}" :options="$users" required />
				<x-form-select name="apartment_id" label="{{ __('Apartment') }}" :options="$apartments" />
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
					<div class="col-md-6">
						<x-form-input name="city" label="{{ __('City') }}" required />
					</div>
					<div class="col-md-6">
						<x-form-input name="area" label="{{ __('Area') }}" required />
					</div>
				</div>
				<x-form-input name="job_title" label="{{ __('Job title') }}" required />
				<x-form-input name="monthly_income" type="number" label="{{ __('Monthly income') }}" required :attrs="['step' => '0.01']" />
				<x-form-input name="monthly_installment" type="number" label="{{ __('Monthly installment') }}" :attrs="['step' => '0.01']" />
				<x-form-select name="years_of_installment" label="{{ __('Years') }}" :options="$years" required />
				<x-form-input name="deposit_percetage" type="number" label="{{ __('Deposit %') }}" required :attrs="['step' => '0.01']" />
				<button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
