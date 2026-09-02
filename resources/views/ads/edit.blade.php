@extends('layouts.app')
@php
	$currentPage = 'ads';
@endphp
@section('title', __('Edit Ad'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Ad') }}</h1>
	<div class="mb-3">
		<a href="{{ route('ads.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Ads')}}</a>
		<a href='{{ route('ads.show', $ad) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('ads.update', $ad->id) }}' enctype="multipart/form-data" novalidate>
				@csrf
				@method('PUT')
				<x-form-input
					name="title_en"
					type="text"
					label="{{__('Title (English)')}}"
					:value="$ad->title_en ?? ''"
					required
				/>
				<x-form-input
					name="title_ar"
					type="text"
					label="{{__('Title (Arabic)')}}"
					:value="$ad->title_ar ?? ''"
					required
				/>
				<x-form-input
					name="image"
					type="file"
					label="{{__('Icon')}}"
                    :attributes="['accept' => 'image/*']"
				/>
				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
