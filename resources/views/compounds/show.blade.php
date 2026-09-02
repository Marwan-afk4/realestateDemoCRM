@extends('layouts.app')
@php
	$currentPage = 'compounds';
@endphp
@section('title', $compound->compound_name)
@section('content')
<div class="container-fluid">
	{{-- Header --}}
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div class="d-flex align-items-center">
			<a href="{{ $compound->developer_id ? route('compounds.index', ['developer_id' => $compound->developer_id]) : route('compounds.index') }}"
			   class="btn btn-outline-secondary me-3">
				<i class="fas fa-arrow-left"></i> {{ __('Back') }}
			</a>
			<div>
				<h1 class="mb-0">{{ $compound->compound_name }}</h1>
				@if($compound->developer)
					<p class="text-muted mb-0">
						{{ __('Developer') }}:
						<a href="{{ route('developers.show', $compound->developer) }}" class="text-decoration-none">
							{{ $compound->developer->name }}
						</a>
					</p>
				@endif
			</div>
		</div>
		<div class="d-flex gap-2 align-items-center">
			@if($compound->favourite)
				<span class="favourite-badge-large favourite-active-large">
					<i class="fas fa-star"></i>
					<span>{{ __('Favourite Compound') }}</span>
				</span>
			@endif
			<a href="{{ route('compounds.edit', $compound) }}" class="btn btn-warning">
				<i class="fas fa-edit"></i> {{ __('Edit') }}
			</a>
		</div>
	</div>

	<div class="row">
		{{-- Left Column --}}
		<div class="col-md-4 mb-4">
			<div class="card">
				{{-- Compound Image --}}
				@if($compound->image)
					<img src="{{ asset('storage/' . $compound->image) }}" alt="{{ $compound->compound_name }}"
						 class="card-img-top compound-image"
						 data-bs-toggle="modal" data-bs-target="#imageModal">
				@else
					<div class="no-image">
						<i class="fas fa-building"></i>
						<p>{{ __('No Image') }}</p>
					</div>
				@endif

				<div class="card-body">
					<h4>{{ $compound->compound_name }}</h4>

					@if($compound->developer)
						<p class="text-muted">
							<i class="fas fa-user-tie"></i>
							<a href="{{ route('developers.show', $compound->developer) }}" class="text-decoration-none">
								{{ $compound->developer->name }}
							</a>
						</p>
					@endif

					<div class="compound-stats mt-3">
						<div class="stat-item">
							<i class="fas fa-home text-primary"></i>
							<span class="fw-bold">{{ number_format($compound->units ?? 0) }}</span>
							<small class="text-muted">{{ __('Units') }}</small>
						</div>

						@if($compound->commission_percentage)
							<div class="stat-item">
								<i class="fas fa-percentage text-success"></i>
								<span class="fw-bold">{{ $compound->commission_percentage }}%</span>
								<small class="text-muted">{{ __('Commission') }}</small>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>

		{{-- Right Column --}}
		<div class="col-md-8">
			{{-- Details Card --}}
			<div class="card mb-4">
				<div class="card-header">
					<h5 class="mb-0">{{ __('Compound Details') }}</h5>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-sm-6 mb-3">
							<strong>{{ __('ID') }}:</strong> #{{ $compound->id }}
						</div>
						<div class="col-sm-6 mb-3">
							<strong>{{ __('Name') }}:</strong> {{ $compound->compound_name }}
						</div>
						<div class="col-sm-6 mb-3">
							<strong>{{ __('Units') }}:</strong> {{ number_format($compound->units ?? 0) }}
						</div>
						@if($compound->commission_percentage)
							<div class="col-sm-6 mb-3">
								<strong>{{ __('Commission') }}:</strong> {{ $compound->commission_percentage }}%
							</div>
						@endif
						<div class="col-sm-6 mb-3">
							<strong>{{ __('Status') }}:</strong>
							@if($compound->favourite)
								<span class="favourite-badge-medium favourite-active-medium">
									<i class="fas fa-star"></i>
									<span>{{ __('Favourite') }}</span>
								</span>
							@else
								<span class="favourite-badge-medium favourite-inactive-medium">
									<i class="far fa-star"></i>
									<span>{{ __('Regular') }}</span>
								</span>
							@endif
						</div>
						<div class="col-sm-6 mb-3">
							<strong>{{ __('Created') }}:</strong> {{ $compound->created_at->format('M d, Y') }}
						</div>
						<div class="col-sm-6 mb-3">
							<strong>{{ __('Updated') }}:</strong> {{ $compound->updated_at->diffForHumans() }}
						</div>
					</div>
				</div>
			</div>

			{{-- Related Information --}}
			@if($compound->developer)
				<div class="card">
					<div class="card-header">
						<h5 class="mb-0">{{ __('Developer Information') }}</h5>
					</div>
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="developer-avatar me-3">
								@if($compound->developer->image)
									<img src="{{ asset('storage/' . $compound->developer->image) }}"
										 alt="{{ $compound->developer->name }}" class="rounded-circle"
										 style="width: 60px; height: 60px; object-fit: cover;">
								@else
									<div class="avatar-placeholder">
										<i class="fas fa-user-tie"></i>
									</div>
								@endif
							</div>
							<div>
								<h6 class="mb-1">{{ $compound->developer->name }}</h6>
								@if($compound->developer->email)
									<p class="text-muted mb-1">
										<i class="fas fa-envelope"></i>
										<a href="mailto:{{ $compound->developer->email }}">{{ $compound->developer->email }}</a>
									</p>
								@endif
								<div class="developer-stats">
									<small class="text-muted">
										<i class="fas fa-building"></i> {{ number_format($compound->developer->units ?? 0) }} {{ __('Total Units') }}
										@if($compound->developer->total_deals)
											| <i class="fas fa-handshake"></i> {{ number_format($compound->developer->total_deals) }} {{ __('Deals') }}
										@endif
									</small>
								</div>
								<a href="{{ route('developers.show', $compound->developer) }}" class="btn btn-sm btn-outline-primary mt-2">
									{{ __('View Developer') }}
								</a>
							</div>
						</div>
					</div>
				</div>
			@endif
		</div>
	</div>
</div>

{{-- Image Modal --}}
@if($compound->image)
	<div class="modal fade" id="imageModal" tabindex="-1">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ $compound->compound_name }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body text-center p-0">
					<img src="{{ asset('storage/' . $compound->image) }}" alt="{{ $compound->compound_name }}" class="img-fluid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
					<a href="{{ asset('storage/' . $compound->image) }}" download class="btn btn-primary">
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
.compound-image {
    height: 250px;
    object-fit: cover;
    cursor: pointer;
    transition: opacity 0.3s ease;
}

.compound-image:hover {
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

.compound-stats {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-item i {
    width: 20px;
    text-align: center;
}

.developer-avatar .avatar-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 24px;
}

.developer-stats {
    margin-top: 4px;
}

/* Favourite Badge Styles */
.favourite-badge-large {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.favourite-active-large {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
    color: #ffffff;
    border: 2px solid #f59e0b;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.favourite-active-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
}

.favourite-active-large i {
    color: #ffffff;
    font-size: 16px;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.3));
}

.favourite-badge-medium {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 18px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.favourite-active-medium {
    background: linear-gradient(135deg, #fcd34d 0%, #f59e0b 100%);
    color: #92400e;
    border: 1px solid #f59e0b;
}

.favourite-active-medium i {
    color: #d97706;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.favourite-inactive-medium {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #64748b;
    border: 1px solid #cbd5e1;
}

.favourite-inactive-medium i {
    color: #94a3b8;
}

@media (max-width: 768px) {
    .compound-stats {
        flex-direction: row;
        flex-wrap: wrap;
    }

    .stat-item {
        flex: 1;
        min-width: 120px;
        border-bottom: none;
        border-right: 1px solid #e9ecef;
        padding: 8px;
        text-align: center;
    }

    .stat-item:last-child {
        border-right: none;
    }

    .favourite-badge-large {
        padding: 8px 12px;
        font-size: 12px;
    }

    .favourite-badge-large span {
        display: none;
    }
}
</style>
@endpush
