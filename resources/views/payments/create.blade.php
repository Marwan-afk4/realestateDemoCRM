@extends('layouts.app')
@php
	$currentPage = 'payments';
@endphp
@section('title', __('Create Payment'))
@section('content')
<div class="container">
	<h1>{{ __('Create Payment') }}</h1>
	<div class="mb-3">
		<a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Payments')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('payments.store') }}' enctype="multipart/form-data" novalidate>
				@csrf
				<x-form-select
					name="payment_method_id"
					type="select"
					label="{{__('Payment Method')}}"
					required
					:options="$paymentMethods"
				/>
				<x-form-input
					name="receipt"
					type="file"
					label="{{__('Receipt')}}"
                    :attributes="['accept' => 'image/*']"
                    required
				/>
				<x-form-select
					name="brocker_id"
					type="select"
					label="{{__('Brocker')}}"
					:options="$brockers"
                    required
				/>
				<x-form-select
					name="plan_id"
					type="select"
					label="{{__('Plan')}}"
					required
					:options="$plans"
				/>
				<x-form-select
					name="status"
					type="select"
					label="{{__('Status')}}"
                    :options="$statuses"
					required
				/>
				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Add') }}</button>
			</form>
		</div>
	</div>
</div>@endsection
