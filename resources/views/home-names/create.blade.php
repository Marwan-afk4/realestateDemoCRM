@extends('layouts.app')
@php
	$currentPage = 'home-names';
@endphp
@section('title', __('Create Home Name'))
@section('content')
<div class="container">
	<h1>{{ __('Create Home Name') }}</h1>
	<div class="mb-3">
		<a href="{{ route('home-names.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Home Names')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('home-names.store') }}' class='needs-validation' novalidate>
				@csrf
				<x-form-input 
					name="name_ar"
					type="text"
					label="{{__('Name Ar')}}"
					required
				/>
				<x-form-input 
					name="name_en"
					type="text"
					label="{{__('Name En')}}"
					required
				/>
				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Add') }}</button>
			</form>
		</div>
	</div>
</div>@endsection