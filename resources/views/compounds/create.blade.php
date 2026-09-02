@extends('layouts.app')
@php
	$currentPage = 'compounds';
@endphp
@section('title', __('Create Compound'))
@section('content')
<div class="container">
	@if($developer)
		<div class="d-flex justify-content-between align-items-center mb-4">
			<div class="d-flex align-items-center">
				<a href="{{ route('compounds.index', ['developer_id' => $developer->id]) }}" class="btn btn-outline-secondary me-3">
					<i class="fas fa-arrow-left"></i> {{ __('Back to Compounds') }}
				</a>
				<div>
					<h1 class="mb-0">{{ __('Create Compound for') }} {{ $developer->name }}</h1>
					<p class="text-muted mb-0">{{ __('Adding a new compound to this developer') }}</p>
				</div>
			</div>
		</div>
	@else
		<h1>{{ __('Create Compound') }}</h1>
		<div class="mb-3">
			<a href="{{ route('compounds.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Compounds')}}</a>
		</div>
	@endif
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('compounds.store') }}' class='needs-validation' novalidate enctype="multipart/form-data">
				@csrf

				<div class="mb-3">
					<label for="developer_id" class="form-label">{{ __('Developer') }} <span class="text-danger">*</span></label>
					<select name="developer_id" id="developer_id" class="form-select @error('developer_id') is-invalid @enderror" required>
						<option value="">{{ __('Select Developer') }}</option>
						@foreach($developers as $dev)
							<option value="{{ $dev->id }}" {{ (old('developer_id', $developerId) == $dev->id) ? 'selected' : '' }}>
								{{ $dev->name }}
							</option>
						@endforeach
					</select>
					@error('developer_id')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<label for="compound_name" class="form-label">{{ __('Compound Name') }} <span class="text-danger">*</span></label>
					<input type="text" name="compound_name" id="compound_name" class="form-control @error('compound_name') is-invalid @enderror"
						   value="{{ old('compound_name') }}" required>
					@error('compound_name')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<label for="image" class="form-label">{{ __('Image') }}</label>
					<input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
					@error('image')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<label for="units" class="form-label">{{ __('Units') }} <span class="text-danger">*</span></label>
					<input type="number" name="units" id="units" class="form-control @error('units') is-invalid @enderror"
						   value="{{ old('units') }}" required min="0">
					@error('units')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<label for="commission_percentage" class="form-label">{{ __('Commission Percentage') }}</label>
					<input type="number" name="commission_percentage" id="commission_percentage"
						   class="form-control @error('commission_percentage') is-invalid @enderror"
						   value="{{ old('commission_percentage') }}" min="0" max="100" step="0.01">
					@error('commission_percentage')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<div class="form-check">
						<input type="hidden" name="favourite" value="0">
						<input type="checkbox" name="favourite" id="favourite" class="form-check-input @error('favourite') is-invalid @enderror"
							   value="1" {{ old('favourite') ? 'checked' : '' }}>
						<label for="favourite" class="form-check-label">{{ __('Mark as Favourite') }}</label>
						@error('favourite')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
				</div>

				<button type='submit' class="btn btn-primary">{{ __('Create Compound') }}</button>
				<a href="{{ $developer ? route('compounds.index', ['developer_id' => $developer->id]) : route('compounds.index') }}"
				   class="btn btn-secondary">{{ __('Cancel') }}</a>
			</form>
		</div>
	</div>
</div>@endsection
