@extends('layouts.app')
@php
	$currentPage = 'home-names';
@endphp
@section('title', __('Edit Home Name'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Home Name') }}</h1>
	<div class="mb-3">
		<a href="{{ route('home-names.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-right"></i> {{__('Back to')}} {{__('Home Names')}}</a>
		<a href='{{ route('home-names.show', $homeName) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('home-names.update', $homeName->id) }}' class='needs-validation' novalidate>
				@csrf
				@method('PUT')
				<x-form-input 
					name="name_ar"
					type="text"
					label="{{__('Name Ar')}}"
					:value="$homeName->name_ar ?? ''"
					required
				/>
				<x-form-input 
					name="name_en"
					type="text"
					label="{{__('Name En')}}"
					:value="$homeName->name_en ?? ''"
					required
				/>
				<button type='submit' class="btn btn-warning btn-sm me-1">{{ __('Save') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection