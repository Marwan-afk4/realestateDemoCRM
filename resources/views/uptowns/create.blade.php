@extends('layouts.app')
@php
	$currentPage = 'uptowns';
@endphp
@section('title', __('Create Unit'))
@section('content')
<div class="container">
	<h1>{{ __('Create Unit') }}</h1>
	<div class="mb-3">
		<a href="{{ route('uptowns.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Units')}}</a>
	</div>
	<div class="main-card mb-3 card">
		<div class="card-body">
			<form method='POST' action='{{ route('uptowns.store') }}' class='needs-validation' novalidate enctype="multipart/form-data">
				@csrf

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="name_en"
							type="text"
							label="{{__('Name (English)')}}"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="name_ar"
							type="text"
							label="{{__('Name (Arabic)')}}"
							required
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<x-form-select
							name="developer_id"
							:options="$developers->pluck('name', 'id')->toArray()"
							label="{{__('Developer')}}"
							required
							id="developer_id"
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label for="compound_id" class="form-label">{{__('Compound')}} <span class="text-danger">*</span></label>
							<select class="form-select @error('compound_id') is-invalid @enderror"
									id="compound_id" name="compound_id" required>
								<option value="">{{__('Select Developer First')}}</option>
							</select>
							@error('compound_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					<div class="col-md-6">
						<x-form-select
							name="uptown_type_id"
							:options="$uptownTypes->pluck('name', 'id')->toArray()"
							label="{{__('Uptown Type')}}"
							required
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<x-form-input
							name="strat_price"
							type="number"
							step="0.01"
							label="{{__('Starting Price')}}"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="delivery_date"
							type="date"
							label="{{__('Delivery Date')}}"
							required
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<x-form-select
							name="type"
							:options="['buy' => 'Buy', 'rent' => 'Rent']"
							label="{{__('Type')}}"
							required
							id="unit_type"
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<x-form-select
							name="status"
							:options="['available' => 'Available', 'sold' => 'Sold', 'reserved' => 'Reserved']"
							label="{{__('Status')}}"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="space"
							type="number"
							step="0.01"
							label="{{__('Space (m²)')}}"
							required
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<x-form-input
							name="bed"
							type="number"
							label="{{__('Bedrooms')}}"
							required
						/>
					</div>
					<div class="col-md-6">
						<x-form-input
							name="bathroom"
							type="number"
							label="{{__('Bathrooms')}}"
							required
						/>
					</div>
				</div>



				<div class="row" id="installments_section">
					<div class="col-md-4">
						<div class="mb-3">
							<label class="form-label">{{__('Cash Payment Available')}}</label>
							<div class="form-check form-switch">
								<input class="form-check-input @error('cash') is-invalid @enderror"
									   type="checkbox" id="cash" name="cash" value="1" {{ old('cash') ? 'checked' : '' }}>
								<label class="form-check-label" for="cash">
									{{__('Available')}}
								</label>
								@error('cash')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="mb-3">
							<label class="form-label">{{__('Installment Payment Available')}}</label>
							<div class="form-check form-switch">
								<input class="form-check-input @error('installment') is-invalid @enderror"
									   type="checkbox" id="installment" name="installment" value="1" {{ old('installment') ? 'checked' : '' }}>
								<label class="form-check-label" for="installment">
									{{__('Available')}}
								</label>
								@error('installment')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>
					</div>
					<div class="col-md-3" id="installment_years_container" style="display: none;">
						<x-form-input
							name="installment_years"
							type="number"
							label="{{__('Installment Years')}}"
							id="installment_years"
						/>
					</div>
					<div class="col-md-3" id="installment_plan_container" style="display: none;">
						<x-form-select
							name="installment_plan"
							:options="['monthly' => 'Monthly', 'yearly' => 'Yearly']"
							label="{{__('Installment Plan')}}"
							id="installment_plan"
						/>
					</div>
					<div class="col-md-3" id="installment_price_container" style="display: none;">
						<x-form-input
							name="installment_price"
							type="number"
							step="0.01"
							label="{{__('Installment Price')}}"
							id="installment_price"
						/>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label for="floor_plan_image" class="form-label">{{__('Floor Plan Image')}}</label>
							<input type="file" class="form-control @error('floor_plan_image') is-invalid @enderror"
								   id="floor_plan_image" name="floor_plan_image" accept="image/*">
							@error('floor_plan_image')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label for="master_plan_image" class="form-label">{{__('Master Plan Image')}}</label>
							<input type="file" class="form-control @error('master_plan_image') is-invalid @enderror"
								   id="master_plan_image" name="master_plan_image" accept="image/*">
							@error('master_plan_image')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>

				<!-- Unit Images Upload -->
				<div class="row">
					<div class="col-12">
						<div class="mb-3">
							<label class="form-label">{{__('Unit Images')}}</label>

							<!-- Drag and Drop Area -->
							<div id="drop-area" class="border border-2 border-dashed rounded p-4 text-center mb-3"
								 style="border-color: #dee2e6; background-color: #f8f9fa; cursor: pointer; transition: all 0.3s ease;">
								<div class="upload-content">
									<i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
									<h5 class="text-muted">{{__('Drag & Drop Images Here')}}</h5>
									<p class="text-muted mb-3">{{__('or click to browse files')}}</p>
									<button type="button" class="btn btn-primary btn-sm" id="browse-btn">
										<i class="fas fa-folder-open me-1"></i> {{__('Browse Images')}}
									</button>
									<p class="small text-muted mt-2 mb-0">{{__('Max 10 images, 2MB each. Supports: JPG, PNG, GIF')}}</p>
								</div>
							</div>

							<!-- Hidden File Input -->
							<input type="file" id="unit_images" name="unit_images[]" accept="image/*" multiple style="display: none;">

							@error('unit_images')
								<div class="text-danger small">{{ $message }}</div>
							@enderror
							@error('unit_images.*')
								<div class="text-danger small">{{ $message }}</div>
							@enderror

							<!-- Selected Images Grid -->
							<div id="selected-images-container" style="display: none;">
								<h6 class="mt-4 mb-3">{{__('Selected Images')}} (<span id="image-count">0</span>/10):</h6>
								<div id="selected-images-grid" class="row g-3"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="mb-3">
							<label for="description_en" class="form-label">{{__('Description (English)')}}</label>
							<textarea class="form-control @error('description_en') is-invalid @enderror"
									  id="description_en" name="description_en" rows="4">{{ old('description_en') }}</textarea>
							@error('description_en')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label for="description_ar" class="form-label">{{__('Description (Arabic)')}}</label>
							<textarea class="form-control @error('description_ar') is-invalid @enderror"
									  id="description_ar" name="description_ar" rows="4">{{ old('description_ar') }}</textarea>
							@error('description_ar')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>

				<button type='submit' class="btn btn-primary btn-sm me-1">{{ __('Create Uptown') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const developerSelect = document.getElementById('developer_id');
    const compoundSelect = document.getElementById('compound_id');

    // All compounds data from server
    const allCompounds = @json($compounds);

    if (!developerSelect || !compoundSelect) {
        console.error('Developer or Compound select elements not found');
        return;
    }

    function updateCompounds(developerId) {
        // Clear compound options
        compoundSelect.innerHTML = '<option value="">{{__("Select Compound")}}</option>';

        if (developerId) {
            // Filter compounds by developer_id
            const filteredCompounds = allCompounds.filter(compound =>
                compound.developer_id == developerId
            );

            if (filteredCompounds.length > 0) {
                filteredCompounds.forEach(compound => {
                    const option = document.createElement('option');
                    option.value = compound.id;
                    option.textContent = compound.compound_name;
                    compoundSelect.appendChild(option);
                });
            } else {
                compoundSelect.innerHTML = '<option value="">{{__("No compounds found for this developer")}}</option>';
            }
        } else {
            compoundSelect.innerHTML = '<option value="">{{__("Select Developer First")}}</option>';
        }
    }

    developerSelect.addEventListener('change', function() {
        const developerId = this.value;
        console.log('Developer selected:', developerId);
        updateCompounds(developerId);
    });

    // Initialize on page load if developer is already selected
    if (developerSelect.value) {
        updateCompounds(developerSelect.value);
    }

    // Enhanced Image Upload with Drag & Drop
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('unit_images');
    const browseBtn = document.getElementById('browse-btn');
    const selectedImagesContainer = document.getElementById('selected-images-container');
    const selectedImagesGrid = document.getElementById('selected-images-grid');
    const imageCount = document.getElementById('image-count');

    let selectedFiles = [];
    const maxFiles = 10;

    if (dropArea && fileInput) {
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        // Handle dropped files
        dropArea.addEventListener('drop', handleDrop, false);

        // Handle browse button click
        browseBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.click();
        });

        // Handle drop area click (but not when clicking the button)
        dropArea.addEventListener('click', function(e) {
            if (e.target === browseBtn || browseBtn.contains(e.target)) {
                return; // Don't trigger if clicking the button
            }
            fileInput.click();
        });

        // Handle file input change
        fileInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropArea.style.borderColor = '#007bff';
        dropArea.style.backgroundColor = '#e3f2fd';
    }

    function unhighlight(e) {
        dropArea.style.borderColor = '#dee2e6';
        dropArea.style.backgroundColor = '#f8f9fa';
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        const newFiles = Array.from(files).filter(file => {
            if (!file.type.startsWith('image/')) {
                alert(`${file.name} is not an image file.`);
                return false;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert(`${file.name} is larger than 2MB.`);
                return false;
            }
            return true;
        });

        // Check total file limit
        if (selectedFiles.length + newFiles.length > maxFiles) {
            alert(`You can only upload maximum ${maxFiles} images.`);
            return;
        }

        // Add new files to selected files
        selectedFiles = [...selectedFiles, ...newFiles];
        updateFileInput();
        displaySelectedImages();
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }
    function displaySelectedImages() {
        if (selectedFiles.length === 0) {
            selectedImagesContainer.style.display = 'none';
            return;
        }

        selectedImagesContainer.style.display = 'block';
        imageCount.textContent = selectedFiles.length;
        selectedImagesGrid.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageCard = document.createElement('div');
                imageCard.className = 'col-md-3 col-sm-4 col-6';
                imageCard.innerHTML = `
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-image-btn"
                                    data-index="${index}" style="padding: 4px 8px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="card-body p-2">
                            <small class="text-muted d-block text-truncate">${file.name}</small>
                            <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                        </div>
                    </div>
                `;
                selectedImagesGrid.appendChild(imageCard);
            };
            reader.readAsDataURL(file);
        });
    }

    const unitTypeSelect = document.getElementById('unit_type');
    const installmentContainer = document.getElementById('installment_years_container');
    const installmentPlanContainer = document.getElementById('installment_plan_container');
    const installmentCheckbox = document.getElementById('installment');

    function toggleInstallmentDetails() {
        if (unitTypeSelect && unitTypeSelect.value === 'rent') {
            installmentCheckbox.checked = true;
        }

        const show = installmentCheckbox.checked || (unitTypeSelect && unitTypeSelect.value === 'rent');
        const displayVal = show ? 'block' : 'none';

        document.getElementById('installment_years_container').style.display = displayVal;
        document.getElementById('installment_plan_container').style.display = displayVal;
        document.getElementById('installment_price_container').style.display = displayVal;
    }

    if (installmentCheckbox) {
        installmentCheckbox.addEventListener('change', toggleInstallmentDetails);
    }
    if (unitTypeSelect) {
        unitTypeSelect.addEventListener('change', toggleInstallmentDetails);
    }
    
    toggleInstallmentDetails();
});
</script>
@endpush
