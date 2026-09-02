@extends('layouts.app')
@php
	$currentPage = 'developers';
@endphp
@section('title', __('Edit Developer'))
@section('content')
<div class="container">
	<h1>{{ __('Edit Developer') }}</h1>
	<div class="mb-3">
		<a href="{{ route('developers.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Developers')}}</a>
		<a href='{{ route('developers.show', $developer) }}' class="btn btn-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('developers.update', $developer->id) }}' enctype="multipart/form-data" novalidate>
				@csrf
				@method('PUT')

				{{-- Basic Information --}}
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0">{{ __('Basic Information') }}</h5>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label for="name_en" class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
									<input type="text" name="name_en" id="name_en" class="form-control @error('name_en') is-invalid @enderror"
										   value="{{ old('name_en', $developer->name_en) }}" required>
									@error('name_en')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label for="name_ar" class="form-label">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
									<input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
										   value="{{ old('name_ar', $developer->name_ar) }}" required>
									@error('name_ar')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label for="email" class="form-label">{{ __('Email') }}</label>
									<input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
										   value="{{ old('email', $developer->email) }}">
									@error('email')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label for="description_en" class="form-label">{{ __('Description (English)') }}</label>
									<textarea name="description_en" id="description_en" class="form-control @error('description_en') is-invalid @enderror"
											  rows="3">{{ old('description_en', $developer->description_en) }}</textarea>
									@error('description_en')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label for="description_ar" class="form-label">{{ __('Description (Arabic)') }}</label>
									<textarea name="description_ar" id="description_ar" class="form-control @error('description_ar') is-invalid @enderror"
											  rows="3">{{ old('description_ar', $developer->description_ar) }}</textarea>
									@error('description_ar')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>

						<div class="mb-3">
							<label for="image" class="form-label">{{ __('Developer Image') }}</label>
							@if($developer->image)
								<div class="mb-2">
									<img src="{{ asset('storage/' . $developer->image) }}" alt="{{ $developer->name }}"
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
					</div>
				</div>

				{{-- Business Information --}}
				<div class="card mb-4">
					<div class="card-header">
						<h5 class="mb-0">{{ __('Business Information') }}</h5>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-3">
								<div class="mb-3">
									<label for="units" class="form-label">{{ __('Units') }}</label>
									<input type="number" name="units" id="units" class="form-control @error('units') is-invalid @enderror"
										   value="{{ old('units', $developer->units) }}" min="0">
									@error('units')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

							<div class="col-md-3">
								<div class="mb-3">
									<label for="total_deals" class="form-label">{{ __('Total Deals') }}</label>
									<input type="number" name="total_deals" id="total_deals" class="form-control @error('total_deals') is-invalid @enderror"
										   value="{{ old('total_deals', $developer->total_deals) }}" min="0">
									@error('total_deals')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
							<div class="col-md-3">
								<div class="mb-3">
									<label for="deals_done" class="form-label">{{ __('Deals Done') }}</label>
									<input type="number" name="deals_done" id="deals_done" class="form-control @error('deals_done') is-invalid @enderror"
										   value="{{ old('deals_done', $developer->deals_done) }}" min="0">
									@error('deals_done')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
							<div class="col-md-3">
								<div class="mb-3">
									<label for="total_profit" class="form-label">{{ __('Total Profit') }}</label>
									<input type="number" name="total_profit" id="total_profit" class="form-control @error('total_profit') is-invalid @enderror"
										   value="{{ old('total_profit', $developer->total_profit) }}" min="0" step="0.01">
									@error('total_profit')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label for="start_date" class="form-label">{{ __('Start Date') }}</label>
									<input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
										   value="{{ old('start_date', $developer->start_date ? $developer->start_date->format('Y-m-d') : '') }}">
									@error('start_date')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label for="end_date" class="form-label">{{ __('End Date') }}</label>
									<input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
										   value="{{ old('end_date', $developer->end_date ? $developer->end_date->format('Y-m-d') : '') }}">
									@error('end_date')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>
					</div>
				</div>

				{{-- Places --}}
				<div class="card mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="mb-0">{{ __('Places') }}</h5>
						<button type="button" class="btn btn-sm btn-primary" onclick="addPlace()">
							<i class="fas fa-plus"></i> {{ __('Add Place') }}
						</button>
					</div>
					<div class="card-body">
						<div id="places-container">
							@forelse($developer->places as $index => $place)
								<div class="place-item mb-3">
									<div class="input-group">
										<input type="text" name="places[]" class="form-control"
											   value="{{ old('places.' . $index, $place->place) }}"
											   placeholder="{{ __('Enter place name') }}">
										<button type="button" class="btn btn-outline-danger" onclick="removePlace(this)">
											<i class="fas fa-trash"></i>
										</button>
									</div>
								</div>
							@empty
								<div class="place-item mb-3">
									<div class="input-group">
										<input type="text" name="places[]" class="form-control" placeholder="{{ __('Enter place name') }}">
										<button type="button" class="btn btn-outline-danger" onclick="removePlace(this)">
											<i class="fas fa-trash"></i>
										</button>
									</div>
								</div>
							@endforelse
						</div>
						<small class="text-muted">{{ __('Add locations where this developer operates') }}</small>
					</div>
				</div>

				{{-- Sales Team --}}
				<div class="card mb-4">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h5 class="mb-0">{{ __('Sales Team') }}</h5>
						<button type="button" class="btn btn-sm btn-primary" onclick="addSalesman()">
							<i class="fas fa-plus"></i> {{ __('Add Salesman') }}
						</button>
					</div>
					<div class="card-body">
						<div id="salesmen-container">
							@forelse($developer->sales_developer as $index => $salesman)
								<div class="salesman-item mb-3">
									<div class="row">
										<div class="col-md-6">
											<input type="text" name="salesmen[{{ $index }}][name]" class="form-control"
												   value="{{ old('salesmen.' . $index . '.name', $salesman->sale_name) }}"
												   placeholder="{{ __('Salesman Name') }}">
										</div>
										<div class="col-md-5">
											<input type="tel" name="salesmen[{{ $index }}][phone]" class="form-control"
												   value="{{ old('salesmen.' . $index . '.phone', $salesman->sale_phone) }}"
												   placeholder="{{ __('Phone Number') }}">
										</div>
										<div class="col-md-1">
											<button type="button" class="btn btn-outline-danger w-100" onclick="removeSalesman(this)">
												<i class="fas fa-trash"></i>
											</button>
										</div>
									</div>
								</div>
							@empty
								<div class="salesman-item mb-3">
									<div class="row">
										<div class="col-md-6">
											<input type="text" name="salesmen[0][name]" class="form-control" placeholder="{{ __('Salesman Name') }}">
										</div>
										<div class="col-md-5">
											<input type="tel" name="salesmen[0][phone]" class="form-control" placeholder="{{ __('Phone Number') }}">
										</div>
										<div class="col-md-1">
											<button type="button" class="btn btn-outline-danger w-100" onclick="removeSalesman(this)">
												<i class="fas fa-trash"></i>
											</button>
										</div>
									</div>
								</div>
							@endforelse
						</div>
						<small class="text-muted">{{ __('Add sales team members for this developer') }}</small>
					</div>
				</div>

				<div class="d-flex gap-2">
					<button type='submit' class="btn btn-warning">{{ __('Update Developer') }}</button>
					<a href="{{ route('developers.show', $developer) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
				</div>
			</form>
		</div>
	</div>
</div>

@push('scripts')
<script>
let placeIndex = {{ $developer->places->count() }};
let salesmanIndex = {{ $developer->sales_developer->count() }};

function addPlace() {
    const container = document.getElementById('places-container');
    const newPlace = document.createElement('div');
    newPlace.className = 'place-item mb-3';
    newPlace.innerHTML = `
        <div class="input-group">
            <input type="text" name="places[]" class="form-control" placeholder="{{ __('Enter place name') }}">
            <button type="button" class="btn btn-outline-danger" onclick="removePlace(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newPlace);
}

function removePlace(button) {
    const container = document.getElementById('places-container');
    if (container.children.length > 1) {
        button.closest('.place-item').remove();
    }
}

function addSalesman() {
    const container = document.getElementById('salesmen-container');
    const newSalesman = document.createElement('div');
    newSalesman.className = 'salesman-item mb-3';
    newSalesman.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="salesmen[${salesmanIndex}][name]" class="form-control" placeholder="{{ __('Salesman Name') }}">
            </div>
            <div class="col-md-5">
                <input type="tel" name="salesmen[${salesmanIndex}][phone]" class="form-control" placeholder="{{ __('Phone Number') }}">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100" onclick="removeSalesman(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newSalesman);
    salesmanIndex++;
}

function removeSalesman(button) {
    const container = document.getElementById('salesmen-container');
    if (container.children.length > 1) {
        button.closest('.salesman-item').remove();
    }
}
</script>
@endpush
@endsection
