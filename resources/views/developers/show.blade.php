@extends('layouts.app')
@php
    $currentPage = 'developers';
@endphp
@section('title', $developer->name)
@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('developers.index') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
            <h1 class="mb-0">{{ $developer->name }}</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('developers.edit', $developer) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> {{ __('Edit') }}
            </a>
            <form action="{{ route('developers.destroy', $developer) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('{{ __('Are you sure you want to delete this developer?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        {{-- Left Column --}}
        <div class="col-md-4 mb-4">
            <div class="card">
                {{-- Developer Image --}}
                @if($developer->image)
                    <img src="{{ asset('storage/' . $developer->image) }}" alt="{{ $developer->name }}"
                         class="card-img-top developer-image"
                         data-bs-toggle="modal" data-bs-target="#imageModal">
                @else
                    <div class="no-image">
                        <i class="fas fa-building"></i>
                        <p>{{ __('No Image') }}</p>
                    </div>
                @endif

                <div class="card-body">
                    <h4>{{ $developer->name }}</h4>
                    @if($developer->email)
                        <p class="text-muted">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:{{ $developer->email }}">{{ $developer->email }}</a>
                        </p>
                    @endif

                    @if($developer->description)
                        <p class="mt-3">{{ $developer->description }}</p>
                    @endif

                    @if($developer->start_date || $developer->end_date)
                        <hr>
                        <div class="timeline">
                            @if($developer->start_date)
                                <div class="timeline-item">
                                    <i class="fas fa-play-circle text-success"></i>
                                    <span>{{ __('Started') }}: {{ $developer->start_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                            @if($developer->end_date)
                                <div class="timeline-item">
                                    <i class="fas fa-flag-checkered text-primary"></i>
                                    <span>{{ __('End Date') }}: {{ $developer->end_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-md-8">
            {{-- Statistics --}}
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="stat-card text-center">
                        <div class="stat-number text-primary">{{ number_format($developer->units ?? 0) }}</div>
                        <div class="stat-label">{{ __('Units') }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="stat-card text-center">
                        <div class="stat-number text-success">{{ number_format($developer->total_deals ?? 0) }}</div>
                        <div class="stat-label">{{ __('Total Deals') }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="stat-card text-center">
                        <div class="stat-number text-info">{{ number_format($developer->deals_done ?? 0) }}</div>
                        <div class="stat-label">{{ __('Completed') }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="stat-card text-center">
                        <div class="stat-number text-warning">${{ number_format($developer->total_profit ?? 0) }}</div>
                        <div class="stat-label">{{ __('Profit') }}</div>
                    </div>
                </div>
            </div>

            {{-- button view compounds --}}
            <div class="d-flex justify-content-end mb-4">
                <a href="{{ route('compounds.index', ['developer_id' => $developer->id]) }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> {{ __('View Compounds') }}
                </a>
            </div>

            {{-- Details --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <strong>{{ __('ID') }}:</strong> #{{ $developer->id }}
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong>{{ __('Created') }}:</strong> {{ $developer->created_at->format('M d, Y') }}
                        </div>
                        <div class="col-sm-6 mb-3">
                            <strong>{{ __('Updated') }}:</strong> {{ $developer->updated_at->diffForHumans() }}
                        </div>
                        @if($developer->total_deals > 0)
                            <div class="col-sm-6 mb-3">
                                <strong>{{ __('Success Rate') }}:</strong>
                                <span class="badge bg-success">{{ round(($developer->deals_done / $developer->total_deals) * 100, 1) }}%</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Portal access --}}
            @can('view-developers')
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Developer portal') }}</h5>
                    <a href="{{ route('developer-portal.index', ['developer_id' => $developer->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('Open portal') }}</a>
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ __('Create a login scoped to this developer\'s compounds, inventory, and authorized brokers.') }}</p>
                    <form method="POST" action="{{ route('developers.portal-users.store', $developer) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6"><input class="form-control" name="first_name" placeholder="{{ __('First name') }}" required></div>
                            <div class="col-md-6"><input class="form-control" name="last_name" placeholder="{{ __('Last name') }}" required></div>
                            <div class="col-md-6"><input class="form-control" name="phone" placeholder="{{ __('Phone') }}" required></div>
                            <div class="col-md-6"><input class="form-control" type="email" name="email" placeholder="{{ __('Email') }}"></div>
                            <div class="col-12"><input class="form-control" type="password" name="password" placeholder="{{ __('Password') }}" required minlength="6"></div>
                            <div class="col-12"><button class="btn btn-primary" type="submit">{{ __('Create portal user') }}</button></div>
                        </div>
                    </form>
                </div>
            </div>
            @endcan

            {{-- Places --}}
            @if($developer->places && $developer->places->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Locations') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($developer->places as $place)
                                <div class="col-md-6 mb-2">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                    {{ $place->place }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Sales Team --}}
            @if($developer->sales_developer && $developer->sales_developer->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Sales Team') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($developer->sales_developer as $sales)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="sales-avatar me-3">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $sales->sale_name }}</div>
                                            <div class="text-muted">
                                                <i class="fas fa-phone"></i>
                                                <a href="tel:{{ $sales->sale_phone }}">{{ $sales->sale_phone }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Image Modal --}}
@if($developer->image)
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $developer->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img src="{{ asset('storage/' . $developer->image) }}" alt="{{ $developer->name }}" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <a href="{{ asset('storage/' . $developer->image) }}" download class="btn btn-primary">
                        <i class="fas fa-download"></i> {{ __('Download') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
.developer-image {
    height: 250px;
    object-fit: cover;
    cursor: pointer;
    transition: opacity 0.3s ease;
}

.developer-image:hover {
    opacity: 0.9;
}

.no-image {
    height: 250px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.no-image i {
    font-size: 48px;
    margin-bottom: 12px;
}

.stat-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    height: 100%;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 500;
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 14px;
}

.sales-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 15px;
    }
}
</style>
@endpush
