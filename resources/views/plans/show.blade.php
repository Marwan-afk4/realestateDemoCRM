@extends('layouts.app')
@php
	$currentPage = 'plans';
@endphp
@section('title', $plan->name)
@section('content')
<div class="container-fluid">
	<h1>{{ $plan->name }}</h1>
	<div class="mb-3">
		<a href="{{ route('plans.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Plans')}}</a>
		<a href='{{ route('plans.edit', $plan) }}' class="btn btn-warning btn-sm me-1">{{ __('Edit') }} <i class="fa fa-edit"></i></a>
		{{-- <a href="{{ route('plans.brockers', $plan) }}" class="list-group-item">{{ __('brocker') }}</a> --}}
		{{-- <a href="{{ route('plans.payments', $plan) }}" class="list-group-item">{{ __('payment') }}</a> --}}
		{{-- <a href="{{ route('plans.users', $plan) }}" class="list-group-item">{{ __('user') }}</a> --}}
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h5 class="text-primary mb-0">{{ __('Plan Details') }}</h5>
				</div>
				<div class="card-body">
					<ul class="list-group list-group-flush">
						<li class="list-group-item">
							<strong>{{ __("Plan ID") }}:</strong> {{ $plan->id }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Plan Name") }}:</strong> {{ $plan->name }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Number of Leads") }}:</strong>
							<span class="badge bg-info">{{ $plan->count_of_leads }}</span>
						</li>
						<li class="list-group-item">
							<strong>{{ __("Period") }}:</strong>
							<span class="badge bg-secondary">{{ $plan->period_in_days }} {{ __('days') }}</span>
						</li>
						<li class="list-group-item">
							<strong>{{ __("Created At") }}:</strong> {{ $plan->created_at?->format('Y-m-d H:i') ?? '-' }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Updated At") }}:</strong> {{ $plan->updated_at?->format('Y-m-d H:i') ?? '-' }}
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h5 class="text-primary mb-0">{{ __('Pricing Information') }}</h5>
				</div>
				<div class="card-body">
					<div class="pricing-info">
						<div class="row mb-3">
							<div class="col-6">
								<strong>{{ __("Original Price") }}:</strong>
							</div>
							<div class="col-6 text-end">
								<span class="h5">{{ number_format($plan->price, 2) }}</span>
							</div>
						</div>

						@if($plan->discount_type && $plan->discount_value)
							<div class="row mb-3">
								<div class="col-6">
									<strong>{{ __("Discount") }}:</strong>
								</div>
								<div class="col-6 text-end">
									<span class="badge bg-success">
										@if($plan->discount_type === 'percentage')
											{{ $plan->discount_value }}% {{ __('Off') }}
										@else
											{{ number_format($plan->discount_value, 2) }} {{ __('Off') }}
										@endif
									</span>
								</div>
							</div>

							<div class="row mb-3">
								<div class="col-6">
									<strong>{{ __("You Save") }}:</strong>
								</div>
								<div class="col-6 text-end">
									<span class="text-success h6">
										{{ number_format($plan->price - $plan->price_after_discount, 2) }}
									</span>
								</div>
							</div>
							<hr>
						@endif

						<div class="row">
							<div class="col-6">
								<strong class="h5">{{ __("Final Price") }}:</strong>
							</div>
							<div class="col-6 text-end">
								<span class="h4 text-primary">
									{{ number_format($plan->price_after_discount, 2) }}
								</span>
							</div>
						</div>

						@if($plan->price != $plan->price_after_discount)
							<div class="alert alert-success mt-3">
								<i class="fa fa-check-circle"></i>
								{{ __('This plan offers a discount of') }}
								<strong>{{ number_format($plan->price - $plan->price_after_discount, 2) }}</strong>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="mt-3">
		{{-- <form method='POST' action='{{ route('plans.destroy', $plan) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
			<input type='hidden' name='_method' value='DELETE'>
			<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
		</form> --}}
	</div>
</div>
@endsection
