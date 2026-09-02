@extends('layouts.app')
@php
	$currentPage = 'plans';
@endphp
@section('title', __('Create Plan'))
@section('content')
<div class="container">
	<h1>{{ __('Create Plan') }}</h1>
	<div class="mb-3">
		<a href="{{ route('plans.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Plans')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('plans.store') }}' class='needs-validation' novalidate>
				@csrf

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
					required
				/>

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="count_of_leads"
							type="number"
							label="{{__('Count Of Leads')}}"
							min="1"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="period_in_days"
							type="number"
							label="{{__('Period In Days')}}"
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
							min="0"
							placeholder="{{__('Enter discount amount or percentage')}}"
						/>
					</div>
				</div>

				<div class="alert alert-info">
					<i class="fa fa-info-circle"></i>
					{{ __('The final price will be calculated automatically based on the discount type and value.') }}
				</div>

				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Create Plan') }}</button>
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

        // Update the info alert
        const alertDiv = document.querySelector('.alert-info');
        if (price > 0) {
            alertDiv.innerHTML = `
                <i class="fa fa-info-circle"></i>
                <strong>Price Calculation:</strong><br>
                Original Price: ${price.toFixed(2)}<br>
                ${discountType && discountValue > 0 ?
                    `Discount: ${discountType === 'percentage' ? discountValue + '%' : discountValue.toFixed(2)}<br>
                     You Save: ${savings.toFixed(2)}<br>` : ''}
                <strong>Final Price: ${finalPrice.toFixed(2)}</strong>
            `;
        } else {
            alertDiv.innerHTML = `
                <i class="fa fa-info-circle"></i>
                The final price will be calculated automatically based on the discount type and value.
            `;
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
