@extends('layouts.app')
@php
	$currentPage = 'compounds';
@endphp
@section('title', __('Edit Compound'))
@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div class="d-flex align-items-center">
			<a href="{{ $compound->developer_id ? route('compounds.index', ['developer_id' => $compound->developer_id]) : route('compounds.index') }}"
			   class="btn btn-outline-secondary me-3">
				<i class="fas fa-arrow-left"></i> {{ __('Back to Compounds') }}
			</a>
			<div>
				<h1 class="mb-0">{{ __('Edit Compound') }}</h1>
				<p class="text-muted mb-0">{{ $compound->compound_name }}</p>
			</div>
		</div>
		<a href='{{ route('compounds.show', $compound) }}' class="btn btn-primary">{{ __("View Details") }} <i class="fa fa-eye"></i></a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('compounds.update', $compound->id) }}' class='needs-validation' novalidate enctype="multipart/form-data">
				@csrf
				@method('PUT')

				<div class="mb-3">
					<label for="developer_id" class="form-label">{{ __('Developer') }} <span class="text-danger">*</span></label>
					<select name="developer_id" id="developer_id" class="form-select @error('developer_id') is-invalid @enderror" required>
						<option value="">{{ __('Select Developer') }}</option>
						@foreach($developers as $dev)
							<option value="{{ $dev->id }}" {{ (old('developer_id', $compound->developer_id) == $dev->id) ? 'selected' : '' }}>
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
						   value="{{ old('compound_name', $compound->compound_name) }}" required>
					@error('compound_name')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<label for="image" class="form-label">{{ __('Image') }}</label>
					@if($compound->image)
						<div class="mb-2">
							<img src="{{ asset('storage/' . $compound->image) }}" alt="{{ $compound->compound_name }}"
								 class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
							<p class="text-muted small">{{ __('Current image') }}</p>
						</div>
					@endif
					<input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
					<small class="text-muted">{{ __('Leave empty to keep current image') }}</small>
					@error('image')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<label for="units" class="form-label">{{ __('Units') }} <span class="text-danger">*</span></label>
					<input type="number" name="units" id="units" class="form-control @error('units') is-invalid @enderror"
						   value="{{ old('units', $compound->units) }}" required min="0">
					@error('units')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<label for="commission_percentage" class="form-label">{{ __('Commission Percentage') }}</label>
					<input type="number" name="commission_percentage" id="commission_percentage"
						   class="form-control @error('commission_percentage') is-invalid @enderror"
						   value="{{ old('commission_percentage', $compound->commission_percentage) }}" min="0" max="100" step="0.01">
					@error('commission_percentage')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>

				<div class="mb-3">
					<div class="form-check">
						<input type="hidden" name="favourite" value="0">
						<input type="checkbox" name="favourite" id="favourite" class="form-check-input @error('favourite') is-invalid @enderror"
							   value="1" {{ old('favourite', $compound->favourite) ? 'checked' : '' }}>
						<label for="favourite" class="form-check-label">{{ __('Mark as Favourite') }}</label>
						@error('favourite')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
				</div>

				<button type='submit' class="btn btn-warning">{{ __('Update Compound') }}</button>
				<a href="{{ $compound->developer_id ? route('compounds.index', ['developer_id' => $compound->developer_id]) : route('compounds.index') }}"
				   class="btn btn-secondary">{{ __('Cancel') }}</a>
			</form>
		</div>
	</div>
</div>
@endsection
