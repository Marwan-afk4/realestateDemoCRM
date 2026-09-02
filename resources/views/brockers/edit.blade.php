@extends('layouts.app')
@php
	$currentPage = 'brockers';
@endphp
@section('title', __('Edit Brocker'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Brocker') }}</h1>
	<div class="mb-3">
		<a href="{{ route('brockers.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Brockers')}}</a>
		<a href='{{ route('brockers.show', $brocker) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('brockers.update', $brocker->id) }}' class='needs-validation' novalidate>
				@csrf
				@method('PUT')

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
							:value="$brocker->user->first_name ?? ''"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="last_name"
							type="text"
							label="{{__('Last Name')}}"
							:value="$brocker->user->last_name ?? ''"
							required
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="email"
							type="email"
							label="{{__('Email')}}"
							:value="$brocker->user->email ?? ''"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="phone"
							type="text"
							label="{{__('Phone')}}"
							:value="$brocker->user->phone ?? ''"
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
					:selected="$brocker->plan_id ?? ''"
					:options="$plans"
				/>

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="profit"
							type="number"
							step="0.01"
							label="{{__('Profit')}}"
							:value="$brocker->profit ?? ''"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="comission_percentage"
							type="number"
							step="0.01"
							label="{{__('Commission Percentage')}}"
							:value="$brocker->comission_percentage ?? ''"
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
							:value="$brocker->number_of_deals ?? ''"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="deals_done"
							type="number"
							label="{{__('Deals Done')}}"
							:value="$brocker->deals_done ?? ''"
							required
						/>
					</div>
				</div>

				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save Changes') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
