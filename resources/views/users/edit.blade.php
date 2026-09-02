@extends('layouts.app')
@php
	$currentPage = 'users';
@endphp
@section('title', __('Edit User'))
@section('content')
<div class="container">
	<h1>{{ __('Edit User') }}</h1>
	<div class="mb-3">
		<a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Users')}}</a>
		<a href='{{ route('users.show', $user) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
		{{-- <a href="{{ route('users.complaints', $user) }}" class="list-group-item">{{ __('complaint') }}</a> --}}
		{{-- <a href="{{ route('users.brockers', $user) }}" class="list-group-item">{{ __('brocker') }}</a> --}}
		{{-- <a href="{{ route('users.payments', $user) }}" class="list-group-item">{{ __('payment') }}</a> --}}
		{{-- <a href="{{ route('users.trainings', $user) }}" class="list-group-item">{{ __('training') }}</a> --}}
		{{-- <a href="{{ route('users.contracts', $user) }}" class="list-group-item">{{ __('contract') }}</a> --}}
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('users.update', $user->id) }}' class='needs-validation' novalidate>
				@csrf
				@method('PUT')
				<x-form-input
					name="first_name"
					type="text"
					label="{{__('First Name')}}"
					:value="$user->first_name ?? ''"
				/>
				<x-form-input
					name="last_name"
					type="text"
					label="{{__('Last Name')}}"
					:value="$user->last_name ?? ''"
				/>
				<x-form-input
					name="email"
					type="text"
					label="{{__('Email')}}"
					:value="$user->email ?? ''"
				/>
                {{-- <x-form-input
                    name="password"
                    type="password"
                    label="{{__('Password')}}"
                    required
                /> --}}
				<x-form-input
					name="phone"
					type="text"
					label="{{__('Phone')}}"
					:value="$user->phone ?? ''"
				/>
				<x-form-input
					name="age"
					type="text"
					label="{{__('Age')}}"
					:value="$user->age ?? ''"
				/>
				<x-form-select
					name="status"
					type="select"
					label="{{__('Status')}}"
					:selected="$user->status->value ?? ''"
                    :options="$statuses"
				/>
				<x-form-input
					name="qualification"
					type="text"
					label="{{__('Qualification')}}"
					:value="$user->qualification ?? ''"
				/>
				<x-form-input
					name="experience_year"
					type="text"
					label="{{__('Experience Year')}}"
					:value="$user->experience_year ?? ''"
				/>
				<x-form-input
					name="governce"
					type="text"
					label="{{__('Governce')}}"
					:value="$user->governce ?? ''"
				/>
				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
