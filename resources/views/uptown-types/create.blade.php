@extends('layouts.app')
@php
	$currentPage = 'uptown-types';
@endphp
@section('title', __('Create Uptown Type'))
@section('content')
<div class="container">
	<h1>{{ __('Create Uptown Type') }}</h1>
	<div class="mb-3">
		<a href="{{ route('uptown-types.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Uptown Types')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('uptown-types.store') }}' class='needs-validation' novalidate>
				@csrf
				<x-form-input
					name="name_en"
					type="text"
					label="{{__('Name (English)')}}"
					required
				/>
				<x-form-input
					name="name_ar"
					type="text"
					label="{{__('Name (Arabic)')}}"
					required
				/>
				<x-form-select
					name="status"
					type="select"
                    :options="$status"
					label="{{__('Status')}}"
					required
				/>
				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Add') }}</button>
			</form>
		</div>
	</div>
</div>@endsection
