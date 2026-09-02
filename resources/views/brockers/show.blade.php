@extends('layouts.app')
@php
	$currentPage = 'brockers';
@endphp
@section('title', $brocker->user?->full_name ?? 'Developer Sale Details')
@section('content')
<div class="container-fluid">
	<h1>{{ $brocker->user?->full_name ?? 'Developer Sale Details' }}</h1>
	<div class="mb-3">
		<a href="{{ route('brockers.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Developer Sales')}}</a>
		<a href='{{ route('brockers.edit', $brocker) }}' class="btn btn-warning btn-sm me-1">{{ __('Edit') }} <i class="fa fa-edit"></i></a>
	</div>
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<h5 class="text-primary mb-3">{{ __('User Information') }}</h5>
					<ul class="list-group list-group-flush">
						<li class="list-group-item">
							<strong>{{ __("First Name") }}:</strong> {{ $brocker->user?->first_name ?? '-' }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Last Name") }}:</strong> {{ $brocker->user?->last_name ?? '-' }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Email") }}:</strong> {{ $brocker->user?->email ?? '-' }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Phone") }}:</strong> {{ $brocker->user?->phone ?? '-' }}
						</li>
					</ul>
				</div>
				<div class="col-md-6">
					<h5 class="text-primary mb-3">{{ __('Developer Sale Information') }}</h5>
					<ul class="list-group list-group-flush">
						<li class="list-group-item">
							<strong>{{ __("Developer Sale ID") }}:</strong> {{ $brocker->id }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Plan") }}:</strong> {{ $brocker->plan?->name ?? '-' }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Profit") }}:</strong> {{ number_format($brocker->profit, 2) }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Commission Percentage") }}:</strong> {{ $brocker->comission_percentage }}%
						</li>
						<li class="list-group-item">
							<strong>{{ __("Number Of Leads") }}:</strong> {{ $brocker->number_of_deals }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Units Sold") }}:</strong> {{ $brocker->units_sold }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Created At") }}:</strong> {{ $brocker->created_at?->diffForHumans() ?? '-' }}
						</li>
						<li class="list-group-item">
							<strong>{{ __("Updated At") }}:</strong> {{ $brocker->updated_at?->diffForHumans() ?? '-' }}
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
    <div class="mt-4">
        <livewire:broker-add-lead :brocker="$brocker" />
    </div>
</div>
@endsection
