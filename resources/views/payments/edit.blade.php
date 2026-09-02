@extends('layouts.app')
@php
	$currentPage = 'payments';
@endphp
@section('title', __('Edit Payment'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Payment') }}</h1>
	<div class="mb-3">
		<a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-right"></i> {{__('Back to')}} {{__('Payments')}}</a>
		<a href='{{ route('payments.show', $payment) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('payments.update', $payment->id) }}' class='needs-validation' novalidate>
				@csrf
				@method('PUT')
				<x-form-select 
					name="payment_method_id"
					type="select"
					label="{{__('Payment Method')}}"
					:selected="$payment->payment_method_id ?? ''"
					required
					:options="$paymentMethods"
				/>
				<x-form-input 
					name="receipt"
					type="text"
					label="{{__('Receipt')}}"
					:value="$payment->receipt ?? ''"
				/>
				<x-form-select 
					name="user_id"
					type="select"
					label="{{__('User')}}"
					:selected="$payment->user_id ?? ''"
					:options="$users"
				/>
				<x-form-select 
					name="brocker_id"
					type="select"
					label="{{__('Brocker')}}"
					:selected="$payment->brocker_id ?? ''"
					:options="$brockers"
				/>
				<x-form-select 
					name="plan_id"
					type="select"
					label="{{__('Plan')}}"
					:selected="$payment->plan_id ?? ''"
					required
					:options="$plans"
				/>
				<x-form-input 
					name="status"
					type="text"
					label="{{__('Status')}}"
					:value="$payment->status ?? ''"
					required
				/>
				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection