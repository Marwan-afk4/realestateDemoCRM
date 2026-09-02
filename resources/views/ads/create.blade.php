@extends('layouts.app')
@php
	$currentPage = 'ads';
@endphp
@section('title', __('Create Ad'))
@section('content')
<div class="container">
	<h1>{{ __('Create Ad') }}</h1>
	<div class="mb-3">
		<a href="{{ route('ads.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Ads')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('ads.store') }}' enctype="multipart/form-data" novalidate>
				@csrf
				<x-form-input
					name="title_en"
					type="text"
					label="{{__('Title (English)')}}"
					required
				/>
				<x-form-input
					name="title_ar"
					type="text"
					label="{{__('Title (Arabic)')}}"
					required
				/>
				<x-form-input
					name="image"
					type="file"
					label="{{__('Icon')}}"
                    :attributes="['accept' => 'image/*']"
                    required
				/>
				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Add') }}</button>
			</form>
		</div>
	</div>
</div>@endsection
