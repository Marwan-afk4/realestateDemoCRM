@extends('layouts.app')
@php
	$currentPage = 'payment-methods';
@endphp
@section('title', __('Create Payment Method'))
@section('content')
<div class="container">
	<h1>{{ __('Create Payment Method') }}</h1>
	<div class="mb-3">
		<a href="{{ route('payment-methods.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Payment Methods')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('payment-methods.store') }}' enctype="multipart/form-data" novalidate>
				@csrf
				<x-form-input
					name="method_name"
					type="text"
					label="{{__('Method Name')}}"
					required
				/>
				<x-form-input
					name="image"
					type="file"
					label="{{__('Icon')}}"
                    :attributes="['accept' => 'image/*']"
                    required
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
