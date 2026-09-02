@extends('layouts.app')
@php
	$currentPage = 'plans';
@endphp
@section('title', __('Plans'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Plans')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('plans.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create Plan')}} <i class="fa fa-plus"></i></a>
		<div class="search-wrapper">
			<form action="{{ route(Route::currentRouteName(),[],false) }}" method="GET" class="d-inline-block">
                <div class="input-group">
					@if (request('keyword'))
						<div class="input-group-append">
							<a class="btn btn-secondary" href="{{ route(Route::currentRouteName(),[],false) }}">
								<i class="fa fa-times"></i>
							</a>
						</div>
					@endif
                    <input type="text" name="keyword" class="form-control" autocomplete="off" placeholder="{{ __('Keyword') }}..." value="{{ request('keyword') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
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
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('name'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Name") }}
							@if($sortField === 'name')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'count_of_leads', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('count_of_leads'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Number Of Leads") }}
							@if($sortField === 'count_of_leads')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					{{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'period_in_days', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('period_in_days'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Period In Days") }}
							@if($sortField === 'period_in_days')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
					<th>{{ __("Period (Days)") }}</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'price', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('price'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Original Price") }}
							@if($sortField === 'price')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>{{ __("Discount") }}</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'price_after_discount', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('price_after_discount'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Final Price") }}
							@if($sortField === 'price_after_discount')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					{{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'discount_type', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('discount_type'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Discount Type") }}
							@if($sortField === 'discount_type')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'discount_value', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('discount_value'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Discount Value") }}
							@if($sortField === 'discount_value')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'price_after_discount', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('price_after_discount'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Price After Discount") }}
							@if($sortField === 'price_after_discount')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('created_at'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Created At") }}
							@if($sortField === 'created_at')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($plans as $plan)
				<tr>
					<td>{{ $plan->id }}</td>
					<td>{{ $plan->name }}</td>
				    <td>{{ $plan->count_of_leads }}</td>
					<td>{{ $plan->period_in_days }}</td>
					<td>{{ number_format($plan->price, 2) }}</td>
					<td>
						@if($plan->discount_type && $plan->discount_value)
							<span class="badge bg-success">
								{{ $plan->discount_type === 'percentage' ? $plan->discount_value . '%' : number_format($plan->discount_value, 2) }}
							</span>
						@else
							<span class="text-muted">{{ __('No Discount') }}</span>
						@endif
					</td>
					<td>
						<strong class="text-primary">{{ number_format($plan->price_after_discount, 2) }}</strong>
						@if($plan->price != $plan->price_after_discount)
							<small class="text-success d-block">
								{{ __('Save') }} {{ number_format($plan->price - $plan->price_after_discount, 2) }}
							</small>
						@endif
					</td>
					{{-- <td>{{ $plan->discount_type }}</td>
					<td>{{ $plan->discount_value }}</td>
					<td>{{ $plan->price_after_discount }}</td> --}}
					<td>{{ $plan->created_at?->diffForHumans()??'-' }}</td>
					<td class="text-center">
						<a href='{{ route('plans.show', $plan) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<form action="{{ route('plans.destroy', $plan) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this plan?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-subtle-danger btn-sm">
                                {{ __('Delete') }} <i class="fa fa-trash"></i>
                            </button>
                        </form>
					</td>
				</tr>
				@endforeach
			</table>
			{{ $plans->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection
