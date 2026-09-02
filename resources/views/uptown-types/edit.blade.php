@extends('layouts.app')
@php
	$currentPage = 'uptown-types';
@endphp
@section('title', __('Edit Uptown Type'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Uptown Type') }}</h1>
	<div class="mb-3">
		<a href="{{ route('uptown-types.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Uptown Types')}}</a>
		<a href='{{ route('uptown-types.show', $uptownType) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
		{{-- <a href="{{ route('uptown-types.uptowns', $uptownType) }}" class="list-group-item">{{ __('uptown') }}</a> --}}
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('uptown-types.update', $uptownType->id) }}' class='needs-validation' novalidate>
				@csrf
				@method('PUT')
				<x-form-input
					name="name_en"
					type="text"
					label="{{__('Name (English)')}}"
					:value="$uptownType->name_en ?? ''"
					required
				/>
				<x-form-input
					name="name_ar"
					type="text"
					label="{{__('Name (Arabic)')}}"
					:value="$uptownType->name_ar ?? ''"
					required
				/>
				<x-form-select
					name="status"
					type="select"
                    :options="$status"
					label="{{__('Status')}}"
					:selected="$uptownType->status->value ?? ''"
					required
				/>
				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
