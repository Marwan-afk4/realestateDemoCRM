@extends('layouts.app')
@php
	$currentPage = 'payment-methods';
@endphp
@section('title', __('Edit Payment Method'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Payment Method') }}</h1>
	<div class="mb-3">
		<a href="{{ route('payment-methods.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Payment Methods')}}</a>
		<a href='{{ route('payment-methods.show', $paymentMethod) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
		{{-- <a href="{{ route('payment-methods.payments', $paymentMethod) }}" class="list-group-item">{{ __('payment') }}</a> --}}
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('payment-methods.update', $paymentMethod->id) }}' enctype="multipart/form-data" novalidate>
				@csrf
				@method('PUT')
				<x-form-input
					name="method_name"
					type="text"
					label="{{__('Method Name')}}"
					:value="$paymentMethod->method_name ?? ''"
				/>
				<x-form-input
					name="image"
					type="file"
					label="{{__('Icon')}}"
                    :attributes="['accept' => 'image/*']"
				/>
				<x-form-select
					name="status"
					type="select"
					label="{{__('Status')}}"
					:selected="$paymentMethod->status->value"
                    :options="$statuses"
				/>
				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
