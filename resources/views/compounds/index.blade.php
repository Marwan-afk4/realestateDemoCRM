@extends('layouts.app')
@php
	$currentPage = 'compounds';
@endphp
@section('title', $developer ? $developer->name . ' - ' . __('Compounds') : __('Compounds'))
@section('content')
<div class="container-fluid">
	{{-- Header with developer context --}}
	@if($developer)
		<div class="d-flex justify-content-between align-items-center mb-4">
			<div class="d-flex align-items-center">
				<a href="{{ route('developers.show', $developer) }}" class="btn btn-outline-secondary me-3">
					<i class="fas fa-arrow-left"></i> {{ __('Back to Developer') }}
				</a>
				<div>
					<h1 class="mb-0">{{ $developer->name }} - {{ __('Compounds') }}</h1>
				</div>
			</div>
		</div>
	@else
		<h1 class="mb-3">{{__('Compounds')}}</h1>
	@endif

	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('compounds.create', $developerId ? ['developer_id' => $developerId] : []) }}" class="btn btn-primary btn-sm me-1">
			{{__('Create Compound')}} <i class="fa fa-plus"></i>
		</a>
		<div class="search-wrapper">
			<form action="{{ route(Route::currentRouteName(), [], false) }}" method="GET">
				<div class="input-group">
					@if(request()->query())
						<a class="btn btn-secondary" href="{{ route(Route::currentRouteName(), $developerId ? ['developer_id' => $developerId] : []) }}">
							<i class="fa fa-times"></i>
						</a>
					@endif

					@if($developerId)
						<input type="hidden" name="developer_id" value="{{ $developerId }}">
					@endif
					<input type="text" name="keyword" class="form-control" placeholder="{{ __('Keyword...') }}" value="{{ request('keyword') }}">
					<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
				</div>
			</form>
		</div>
	</div>
	<div class='main-card mb-3 card'>
		<div class='card-body'>
			<table class="mb-0 table table-hover">
				<tr>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Id") }}
							@if($sortField === 'id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>

					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'compound_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('compound_name'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Compound Name") }}
							@if($sortField === 'compound_name')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'image', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('image'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Image") }}
							@if($sortField === 'image')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'units', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('units'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Units") }}
							@if($sortField === 'units')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'commission_percentage', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('commission_percentage'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Commission Percentage") }}
							@if($sortField === 'commission_percentage')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'favourite', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('favourite'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Favourite") }}
							@if($sortField === 'favourite')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('created_at'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Created At") }}
							@if($sortField === 'created_at')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($compounds as $compound)
				<tr>
					<td>{{ $compound->id }}</td>

					<td>{{ $compound->compound_name }}</td>
					<td>
						@if($compound->image)
							<img src="{{ asset('storage/' . $compound->image) }}" alt="{{ $compound->compound_name }}"
								 class="img-thumbnail compound-image-thumb" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
								 data-bs-toggle="modal" data-bs-target="#imageModal-{{ $compound->id }}">
						@else
							<span class="text-muted">{{ __('No Image') }}</span>
						@endif
					</td>
					<td>{{ number_format($compound->units ?? 0) }}</td>
					<td>{{ $compound->commission_percentage }}%</td>
					<td>
						@if($compound->favourite)
							<span class="favourite-badge favourite-active">
								<i class="fas fa-star"></i>
								<span class="favourite-text">{{ __('Favourite') }}</span>
							</span>
						@else
							<span class="favourite-badge favourite-inactive">
								<i class="far fa-star"></i>
								<span class="favourite-text">{{ __('Regular') }}</span>
							</span>
						@endif
					</td>
					<td>{{ $compound->created_at->format('M d, Y') }}</td>
					<td class="text-center">
						@if($compound->uptwons_count > 0)
							<a href="{{ route('uptowns.index', ['compound_id' => $compound->id]) }}" class="btn btn-subtle-info btn-sm me-1">
								{{ __('Uptowns') }} <i class="fa fa-building"></i>
							</a>
						@endif
						<a href='{{ route('compounds.show', $compound) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<a href='{{ route('compounds.edit', $compound) }}' class="btn btn-subtle-warning btn-sm me-1">{{ __("Edit") }} <i class="fa fa-edit"></i></a>
						{{-- <form method='POST' action='{{ route('compounds.destroy', $compound) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
							<input type='hidden' name='_method' value='DELETE'>
							<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
						</form> --}}
					</td>
				</tr>
				@endforeach
			</table>
			{{ $compounds->links('pagination::custom') }}
		</div>
	</div>
</div>

{{-- Image Modals --}}
@foreach($compounds as $compound)
	@if($compound->image)
		<div class="modal fade" id="imageModal-{{ $compound->id }}" tabindex="-1">
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
@endforeach

@push('styles')
<style>
.compound-image-thumb {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.compound-image-thumb:hover {
    opacity: 0.8;
    transform: scale(1.05);
}

.favourite-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.favourite-active {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #b45309;
    border: 1px solid #f59e0b;
}

.favourite-active:hover {
    background: linear-gradient(135deg, #ffed4e 0%, #fbbf24 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
}

.favourite-active i {
    color: #d97706;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.favourite-inactive {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    color: #64748b;
    border: 1px solid #cbd5e1;
}

.favourite-inactive:hover {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(100, 116, 139, 0.2);
}

.favourite-inactive i {
    color: #94a3b8;
}

.favourite-text {
    font-size: 11px;
    font-weight: 700;
}

@media (max-width: 768px) {
    .favourite-badge {
        padding: 4px 8px;
        font-size: 10px;
    }

    .favourite-text {
        display: none;
    }
}
</style>
@endpush
@endsection
