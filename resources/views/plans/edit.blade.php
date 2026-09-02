@extends('layouts.app')
@php
	$currentPage = 'plans';
@endphp
@section('title', __('Edit Plan'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Plan') }}</h1>
	<div class="mb-3">
		<a href="{{ route('plans.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Plans')}}</a>
		<a href='{{ route('plans.show', $plan) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
		{{-- <a href="{{ route('plans.brockers', $plan) }}" class="list-group-item">{{ __('brocker') }}</a> --}}
		{{-- <a href="{{ route('plans.payments', $plan) }}" class="list-group-item">{{ __('payment') }}</a> --}}
		{{-- <a href="{{ route('plans.users', $plan) }}" class="list-group-item">{{ __('user') }}</a> --}}
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('plans.update', $plan->id) }}' class='needs-validation' novalidate>
				@csrf
				@method('PUT')

				{{-- Basic Plan Information --}}
				<div class="row mb-4">
					<div class="col-12">
						<h5 class="text-primary">{{ __('Plan Information') }}</h5>
						<hr>
					</div>
				</div>

				<x-form-input
					name="name"
					type="text"
					label="{{__('Plan Name')}}"
					:value="$plan->name ?? ''"
					required
				/>

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="count_of_leads"
							type="number"
							label="{{__('Count Of Leads')}}"
							:value="$plan->count_of_leads ?? ''"
							min="1"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="period_in_days"
							type="number"
							label="{{__('Period In Days')}}"
							:value="$plan->period_in_days ?? ''"
							min="1"
							required
						/>
					</div>
				</div>

				<x-form-input
					name="price"
					type="number"
					step="0.01"
					label="{{__('Original Price')}}"
					:value="$plan->price ?? ''"
					min="0"
					required
				/>

				{{-- Discount Information --}}
				<div class="row mb-4 mt-4">
					<div class="col-12">
						<h5 class="text-primary">{{ __('Discount Information') }} <small class="text-muted">({{ __('Optional') }})</small></h5>
						<hr>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<x-form-select
							name="discount_type"
							label="{{__('Discount Type')}}"
							:selected="$plan->discount_type ?? ''"
							:options="[
								'' => __('No Discount'),
								'fixed' => __('Fixed Amount'),
								'percentage' => __('Percentage')
							]"
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="discount_value"
							type="number"
							step="0.01"
							label="{{__('Discount Value')}}"
							:value="$plan->discount_value ?? ''"
							min="0"
							placeholder="{{__('Enter discount amount or percentage')}}"
						/>
					</div>
				</div>

				{{-- Current Price Display --}}
				<div class="alert alert-info">
					<div class="row">
						<div class="col-md-4">
							<strong>{{ __('Original Price') }}:</strong> {{ number_format($plan->price, 2) }}
						</div>
						<div class="col-md-4">
							<strong>{{ __('Discount') }}:</strong>
							@if($plan->discount_type && $plan->discount_value)
								{{ $plan->discount_type === 'percentage' ? $plan->discount_value . '%' : number_format($plan->discount_value, 2) }}
							@else
								{{ __('No Discount') }}
							@endif
						</div>
						<div class="col-md-4">
							<strong>{{ __('Final Price') }}:</strong> {{ number_format($plan->price_after_discount, 2) }}
						</div>
					</div>
					<hr>
					<i class="fa fa-info-circle"></i>
					{{ __('The final price will be recalculated automatically when you save changes.') }}
				</div>

				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save Changes') }}</button>
			</form>
		</div>
	</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.querySelector('input[name="price"]');
    const discountTypeSelect = document.querySelector('select[name="discount_type"]');
    const discountValueInput = document.querySelector('input[name="discount_value"]');

    function calculateFinalPrice() {
        const price = parseFloat(priceInput.value) || 0;
        const discountType = discountTypeSelect.value;
        const discountValue = parseFloat(discountValueInput.value) || 0;

        let finalPrice = price;
        let savings = 0;

        if (discountType === 'fixed' && discountValue > 0) {
            finalPrice = Math.max(0, price - discountValue);
            savings = price - finalPrice;
        } else if (discountType === 'percentage' && discountValue > 0) {
            finalPrice = Math.max(0, price - (price * discountValue / 100));
            savings = price - finalPrice;
        }

        // Update the pricing display in the alert
        const alertDiv = document.querySelector('.alert-info');
        if (alertDiv && price > 0) {
            const originalContent = alertDiv.innerHTML;
            const newContent = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Original Price:</strong> ${price.toFixed(2)}
                    </div>
                    <div class="col-md-4">
                        <strong>Discount:</strong>
                        ${discountType && discountValue > 0 ?
                            (discountType === 'percentage' ? discountValue + '%' : discountValue.toFixed(2)) :
                            'No Discount'}
                    </div>
                    <div class="col-md-4">
                        <strong>Final Price:</strong> ${finalPrice.toFixed(2)}
                    </div>
                </div>
                <hr>
                <i class="fa fa-info-circle"></i>
                The final price will be recalculated automatically when you save changes.
                ${savings > 0 ? `<br><strong class="text-success">You will save: ${savings.toFixed(2)}</strong>` : ''}
            `;
            alertDiv.innerHTML = newContent;
        }
    }

    // Add event listeners
    if (priceInput) priceInput.addEventListener('input', calculateFinalPrice);
    if (discountTypeSelect) discountTypeSelect.addEventListener('change', calculateFinalPrice);
    if (discountValueInput) discountValueInput.addEventListener('input', calculateFinalPrice);
});
</script>
@endpush
@endsection
