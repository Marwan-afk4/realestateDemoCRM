@extends('layouts.app')
@php
	$currentPage = 'users';
@endphp
@section('title', __('Create User'))
@section('content')
<div class="container">
	<h1>{{ __('Create User') }}</h1>
	<div class="mb-3">
		<a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Users')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('users.store') }}' class='needs-validation' novalidate>
				@csrf
				<x-form-input
					name="first_name"
					type="text"
					label="{{__('First Name')}}"
					required
				/>
				<x-form-input
					name="last_name"
					type="text"
					label="{{__('Last Name')}}"
					required
				/>
				<x-form-input
					name="email"
					type="text"
					label="{{__('Email')}}"
					required
				/>
                <x-form-input
                    name="password"
                    type="password"
                    label="{{__('Password')}}"
                    required
                />
				<x-form-input
					name="phone"
					type="text"
					label="{{__('Phone')}}"
					required
				/>
				<x-form-input
					name="age"
					type="text"
					label="{{__('Age')}}"
				/>
				<x-form-select
					name="status"
					type="select"
					label="{{__('Status')}}"
                    :options="$statuses"
					required
				/>
				<x-form-input
					name="qualification"
					type="text"
					label="{{__('Qualification')}}"
				/>
				<x-form-input
					name="experience_year"
					type="text"
					label="{{__('Experience Year')}}"
					required
				/>
				<x-form-input
					name="governce"
					type="text"
					label="{{__('Governce')}}"
				/>
				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Add') }}</button>
			</form>
		</div>
	</div>
</div>@endsection
