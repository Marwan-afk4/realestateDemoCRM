@extends('layouts.app')
@php
    $currentPage = 'uptowns';
@endphp
@section('title', __('Unit Details'))
@section('content')
    <div class="container">
        <h1>{{ __('Unit Details') }}: {{ $uptown->name }}</h1>
        <div class="mb-3">
            <a href="{{ route('uptowns.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i>
                {{ __('Back to') }} {{ __('Units') }}</a>
            <a href="{{ route('uptowns.edit', $uptown) }}" class="btn btn-warning btn-sm me-1"> <i class="fa fa-edit"></i>
                {{ __('Edit') }}</a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Basic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ __('Name') }}:</strong></td>
                                        <td>
                                            <strong>
                                                {{ $uptown->name }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Unit Code') }}:</strong></td>
                                        <td>
                                            <strong>
                                                {{ $uptown->code }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Developer') }}:</strong></td>
                                        <td>
                                            @if ($uptown->compound && $uptown->compound->developer)
                                                <a href="{{ route('developers.show', $uptown->compound->developer) }}">
                                                    {{ $uptown->compound->developer->name }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Compound') }}:</strong></td>
                                        <td>
                                            @if ($uptown->compound)
                                                <a href="{{ route('compounds.show', $uptown->compound) }}">
                                                    {{ $uptown->compound->compound_name }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Type') }}:</strong></td>
                                        <td>
                                            @if ($uptown->uptownType)
                                                <a href="{{ route('uptown-types.show', $uptown->uptownType) }}">
                                                    {{ $uptown->uptownType->name }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Starting Price') }}:</strong></td>
                                        <td>${{ number_format($uptown->strat_price) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Status') }}:</strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $uptown->status === 'available' ? 'success' : ($uptown->status === 'sold' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($uptown->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Listing Type') }}:</strong></td>
                                        <td>
                                            <span class="badge {{ $uptown->type === 'rent' ? 'bg-info' : 'bg-secondary' }}">
                                                {{ ucfirst($uptown->type) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ __('Space') }}:</strong></td>
                                        <td>{{ $uptown->space }} m²</td>
                                    </tr>
                                    @if($uptown->garden_space)
                                    <tr>
                                        <td><strong>{{ __('Garden Space') }}:</strong></td>
                                        <td>{{ $uptown->garden_space }} m²</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>{{ __('Bedrooms') }}:</strong></td>
                                        <td>{{ $uptown->bed }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Bathrooms') }}:</strong></td>
                                        <td>{{ $uptown->bathroom }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Delivery Date') }}:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($uptown->delivery_date)->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Commission Price') }}:</strong></td>
                                        <td>${{ number_format($uptown->commission_price) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Created At') }}:</strong></td>
                                        <td>{{ $uptown->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-card mb-3 card">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Payment Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6>{{ __('Cash Payment') }}</h6>
                                    <h4 class="{{ $uptown->cash ? 'text-success' : 'text-danger' }}">
                                        {{ $uptown->cash ? __('Available') : __('Not Available') }}
                                    </h4>
                                    @if ($uptown->cash)
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6>{{ __('Installment Payment') }}</h6>
                                    <h4 class="{{ $uptown->installment ? 'text-success' : 'text-danger' }}">
                                        {{ $uptown->installment ? __('Available') : __('Not Available') }}
                                    </h4>
                                    @if ($uptown->installment)
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                    @endif
                                </div>
                            </div>
                            @if ($uptown->installment)
                            <div class="col-md-2">
                                <div class="text-center">
                                    <h6>{{ __('Years') }}</h6>
                                    <h4 class="text-info">{{ $uptown->installment_years }}</h4>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <h6>{{ __('Plan') }}</h6>
                                    <h4 class="text-info">{{ ucfirst($uptown->installment_plan) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <h6>{{ __('Installment Price') }}</h6>
                                    <h4 class="text-success">${{ number_format($uptown->installment_price, 2) }}</h4>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($uptown->description)
                    <div class="main-card mb-3 card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Description') }}</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $uptown->description }}</p>
                        </div>
                    </div>
                @endif

                @if ($uptown->latitude && $uptown->longitude)
                    <div class="main-card mb-3 card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Location') }}</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>{{ __('Latitude') }}:</strong> {{ $uptown->latitude }}</p>
                            <p><strong>{{ __('Longitude') }}:</strong> {{ $uptown->longitude }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                @if ($uptown->floor_plan_image || $uptown->master_plan_image || $uptown->unit_plan)
                    <div class="main-card mb-3 card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Images') }}</h5>
                        </div>
                        <div class="card-body">
                            @if ($uptown->unit_plan)
                                <div class="mb-3">
                                    <h6>{{ __('Unit Plan') }}</h6>
                                    <img src="{{ $uptown->unit_plan_url }}" alt="Unit Plan"
                                        class="img-fluid rounded"
                                        style="cursor: pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#imageModal"
                                        data-image-src="{{ $uptown->unit_plan_url }}"
                                        data-image-title="Unit Plan">
                                </div>
                            @endif

                            @if ($uptown->floor_plan_image)
                                <div class="mb-3">
                                    <h6>{{ __('Floor Plan') }}</h6>
                                    <img src="{{ $uptown->floor_plan_image_url }}" alt="Floor Plan"
                                        class="img-fluid rounded">
                                </div>
                            @endif

                            @if ($uptown->master_plan_image)
                                <div class="mb-3">
                                    <h6>{{ __('Master Plan') }}</h6>
                                    <img src="{{ $uptown->master_plan_image_url }}" alt="Master Plan"
                                        class="img-fluid rounded">
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="main-card mb-3 card">
                    <div class="card-header">
                        <h5 class="card-title">{{ __('Statistics') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h6>{{ __('Total Leads') }}</h6>
                                <h4 class="text-primary">{{ $uptown->leads->count() }}</h4>
                            </div>
                            {{-- <div class="col-6">
							<h6>{{ __('Total Deals') }}</h6>
							<h4 class="text-success">{{ $uptown->deals->count() }}</h4>
						</div> --}}
                        </div>
                    </div>
                </div>

                @if ($uptown->images->count() > 0)
                    <div class="main-card mb-3 card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Unit Images') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($uptown->images->take(6) as $image)
                                    <div class="col-md-4 col-6 mb-3">
                                        <img src="{{ asset('storage/' . $image->image) }}"
                                             alt="Unit Image"
                                             class="img-fluid rounded shadow-sm"
                                             style="cursor: pointer; height: 200px; width: 100%; object-fit: cover;"
                                             data-bs-toggle="modal"
                                             data-bs-target="#imageModal"
                                             data-image-src="{{ asset('storage/' . $image->image) }}"
                                             data-image-title="Unit Image">
                                    </div>
                                @endforeach
                            </div>
                            @if ($uptown->images->count() > 6)
                                <p class="text-muted mt-2">{{ __('And') }} {{ $uptown->images->count() - 6 }} {{ __('more images') }}...</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Single Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white" id="imageModalLabel">{{ __('Image View') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" alt="" class="img-fluid" style="max-height: 80vh; width: auto;">
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>{{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('styles')
<style>
    .modal-content.bg-dark {
        background-color: #1a1a1a !important;
    }

    #modalImage {
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle single image modal
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('imageModalLabel');

    // When single image modal is about to show
    imageModal.addEventListener('show.bs.modal', function(event) {
        const trigger = event.relatedTarget;
        const imageSrc = trigger.getAttribute('data-image-src');
        const imageTitle = trigger.getAttribute('data-image-title');

        modalImage.src = imageSrc;
        modalImage.alt = imageTitle;
        modalTitle.textContent = imageTitle;
    });

    // Clear modal content when hidden
    imageModal.addEventListener('hidden.bs.modal', function() {
        modalImage.src = '';
        modalImage.alt = '';
        modalTitle.textContent = '{{ __("Image View") }}';
    });

    // Handle clicking on images in the all images modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('#all-images-grid .image-container')) {
            // Close the all images modal first
            const allImagesModal = bootstrap.Modal.getInstance(document.getElementById('allImagesModal'));
            if (allImagesModal) {
                allImagesModal.hide();
            }
        }
    });

    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (imageModal.classList.contains('show') || document.getElementById('allImagesModal').classList.contains('show')) {
            if (e.key === 'Escape') {
                // Let Bootstrap handle the ESC key
                return;
            }
        }
    });

    // Add loading animation for images
    const galleryImages = document.querySelectorAll('.gallery-image');
    galleryImages.forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });

        img.addEventListener('error', function() {
            this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5JbWFnZSBub3QgZm91bmQ8L3RleHQ+PC9zdmc+';
            this.alt = 'Image not found';
        });
    });

    // Smooth scroll to top when modals open
    document.getElementById('allImagesModal').addEventListener('shown.bs.modal', function() {
        this.querySelector('.modal-body').scrollTop = 0;
    });
});
</script>
@endpush
