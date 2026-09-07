@extends('layouts.app')
@php
	$currentPage = 'contract-agreements';
@endphp
@section('title', __('Create Contract Agreement'))
@section('content')
<div class="container">
	<h1>{{ __('Create Contract Agreement') }}</h1>
	<div class="mb-3">
		<a href="{{ route('contract-agreements.index') }}" class="btn btn-secondary btn-sm me-1">
			<i class="fa fa-arrow-left"></i> {{ __('Back to') }} {{ __('Contract Agreements') }}
		</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method="POST" action="{{ route('contract-agreements.store') }}" novalidate>
				@csrf
				<x-form-select name="contract_id" label="{{ __('Contract') }}" :options="$contracts" required />
				<x-form-select name="user_id" label="{{ __('User') }}" :options="$users" required />
				<button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
