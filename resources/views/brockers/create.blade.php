@extends('layouts.app')
@php
	$currentPage = 'brockers';
@endphp
@section('title', __('Create Brocker'))
@section('content')
<div class="container">
	<h1>{{ __('Create Brocker') }}</h1>
	<div class="mb-3">
		<a href="{{ route('brockers.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Brockers')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('brockers.store') }}' class='needs-validation' novalidate>
				@csrf

				{{-- User Information Section --}}
				<div class="row mb-4">
					<div class="col-12">
						<h5 class="text-primary">{{ __('User Information') }}</h5>
						<hr>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="first_name"
							type="text"
							label="{{__('First Name')}}"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="last_name"
							type="text"
							label="{{__('Last Name')}}"
							required
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<x-form-input
							name="email"
							type="email"
							label="{{__('Email')}}"
							required
						/>
					</div>
                    <div class="col-md-4">
                        <x-form-input
                            name="password"
                            type="password"
                            label="{{__('Password')}}"
                            required
                        />
                    </div>
					<div class="col-md-4">
						<x-form-input
							name="phone"
							type="text"
							label="{{__('Phone')}}"
							required
						/>
					</div>
				</div>

				{{-- Broker Information Section --}}
				<div class="row mb-4 mt-4">
					<div class="col-12">
						<h5 class="text-primary">{{ __('Broker Information') }}</h5>
						<hr>
					</div>
				</div>

				<x-form-select
					name="plan_id"
					type="select"
					label="{{__('Plan')}}"
					:options="$plans"
				/>

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="profit"
							type="number"
							step="0.01"
							label="{{__('Profit')}}"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="comission_percentage"
							type="number"
							step="0.01"
							label="{{__('Commission Percentage')}}"
							required
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="number_of_deals"
							type="number"
							label="{{__('Number Of Deals')}}"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="deals_done"
							type="number"
							label="{{__('Deals Done')}}"
							required
						/>
					</div>
				</div>

				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Add Broker') }}</button>
			</form>
		</div>
	</div>
</div>@endsection
