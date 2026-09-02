@extends('layouts.app')
@php
	$currentPage = 'uptowns';
@endphp
@section('title', __('Units'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Units')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('uptowns.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create Uptown')}} <i class="fa fa-plus"></i></a>
		<div class="search-wrapper">
			<form action="{{ route(Route::currentRouteName(), [], false) }}" method="GET">
				<div class="input-group">
					@if(request()->query())
						<a class="btn btn-secondary" href="{{ route(Route::currentRouteName(), [], false) }}">
							<i class="fa fa-times"></i>
						</a>
					@endif

					@if(request('compound_id'))
						<input type="hidden" name="compound_id" value="{{ request('compound_id') }}">
					@endif
					<input type="text" name="keyword" class="form-control" placeholder="{{ __('Search by name, description, status...') }}" value="{{ request('keyword') }}">
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
							{{ __("Id") }}
							@if($sortField === 'id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							{{ __("Name") }}
							@if($sortField === 'name')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>{{ __("Unit Code") }}</th>
					<th>{{ __("Developer") }}</th>
					<th>{{ __("Compound") }}</th>
					<th>{{ __("Type") }}</th>
					<th>{{ __("Listing") }}</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'strat_price', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							{{ __("Price") }}
							@if($sortField === 'strat_price')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							{{ __("Status") }}
							@if($sortField === 'status')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>{{ __("Space") }}</th>
					<th>{{ __("Beds/Baths") }}</th>
					<th>{{ __("Payment") }}</th>
					<th>{{ __("Delivery") }}</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($uptowns as $uptown)
				<tr>
					<td>{{ $uptown->id }}</td>
					<td>
                        <strong>
                                {{ $uptown->name }}
                        </strong>
                    </td>
					<td>{{ $uptown->code ?? '-' }}</td>
					<td>
                        @if ($uptown->compound && $uptown->compound->developer)
                            <a href="{{ route('developers.show', $uptown->compound->developer) }}">
                                {{ $uptown->compound->developer->name }}
                            </a>
							@else
							-
						@endif
                    </td>
					<td>
                        @if ($uptown->compound)
                            <a href="{{ route('compounds.show', $uptown->compound) }}">
                                {{ $uptown->compound->compound_name }}
                            </a>
							@else
							-
						@endif
                    </td>
					<td>
                        @if ($uptown->uptownType)
                            <a href="{{ route('uptown-types.show', $uptown->uptownType) }}">
                                {{ $uptown->uptownType->name }}
                            </a>
							@else
							-
						@endif
                    </td>
					<td>
						<span class="badge {{ $uptown->type === 'rent' ? 'bg-info' : 'bg-secondary' }}">
							{{ ucfirst($uptown->type) }}
						</span>
					</td>
					<td>${{ number_format($uptown->strat_price) }}</td>
					<td>
						<span class="badge bg-{{ $uptown->status === 'available' ? 'success' : ($uptown->status === 'sold' ? 'danger' : 'warning') }}">
							{{ ucfirst($uptown->status) }}
						</span>
					</td>
					<td>{{ $uptown->space }} m²</td>
					<td>{{ $uptown->bed }}🛏 / {{ $uptown->bathroom }}🚿</td>
					<td>
						@if($uptown->cash)
							<span class="badge bg-success me-1">Cash</span>
						@endif
						@if($uptown->installment)
							<span class="badge bg-primary me-1">Installment</span>
							@if($uptown->installment_plan)
								<small class="text-muted d-block">
									{{ ucfirst($uptown->installment_plan) }}
									@if($uptown->installment_years) &middot; {{ $uptown->installment_years }}yr @endif
								</small>
							@endif
							@if($uptown->installment_price)
								<small class="text-success d-block">${{ number_format($uptown->installment_price, 0) }}/{{ $uptown->installment_plan }}</small>
							@endif
						@endif
						@if(!$uptown->cash && !$uptown->installment)
							<span class="badge bg-secondary">No Payment Options</span>
						@endif
					</td>
					<td>{{ $uptown->delivery_date ? \Carbon\Carbon::parse($uptown->delivery_date)->format('M Y') : '-' }}</td>
					<td class="text-center">
						<a href='{{ route('uptowns.show', $uptown) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<form method='POST' action='{{ route('uptowns.destroy', $uptown) }}' style="display: inline;" onsubmit='return confirm("Are you sure you want to delete this uptown?")'>
							@csrf
							@method('DELETE')
							<button type='submit' class="btn btn-subtle-danger btn-sm">{{ __('Delete') }} <i class="fa fa-trash"></i></button>
						</form>
					</td>
				</tr>
				@endforeach
			</table>
			{{ $uptowns->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection
