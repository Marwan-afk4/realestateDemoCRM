@extends('layouts.app')
@php
	$currentPage = 'brockers';
@endphp
@section('title', __('Developer Sales'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Developer Sales')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('brockers.create') }}" class="btn btn-primary btn-sm me-1">{{__('Add Developer Sale')}} <i class="fa fa-plus"></i></a>
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
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'user_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('user_id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Name") }}
							@if($sortField === 'user_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'user_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('user_id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Email") }}
							@if($sortField === 'user_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
                    </th>
					<th><a href="{{ request()->fullUrlWithQuery(['sort' => 'user_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('user_id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Phone") }}
							@if($sortField === 'user_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a></th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'plan_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('plan_id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Plan") }}
							@if($sortField === 'plan_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					{{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'profit', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('profit'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Profit") }}
							@if($sortField === 'profit')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'number_of_deals', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('number_of_deals'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Number Of Deals") }}
							@if($sortField === 'number_of_deals')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					{{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'comission_percentage', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('comission_percentage'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Comission Percentage") }}
							@if($sortField === 'comission_percentage')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'deals_done', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('deals_done'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Deals Done") }}
							@if($sortField === 'deals_done')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
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
				@foreach($brockers as $brocker)
				<tr>
					<td>{{ $brocker->id }}</td>
					<td>
						{{ $brocker->user?->full_name ?? '' }}
					</td>
					<td>
                        <a href="mailto:{{ $brocker->user?->email ?? '' }}">{{ $brocker->user?->email ?? '' }}</a>
                    </td>
					<td>{{ $brocker->user?->phone ?? '' }}</td>
					<td>
						@if ($brocker->plan)
							<a href='{{ route('plans.show', $brocker->plan) }}'>
								{{$brocker->plan?->name ?? '' }}
							</a>
						@endif
					</td>
					<td>{{ $brocker->number_of_deals }}</td>
					<td>{{ $brocker->deals_done }}</td>
					<td>{{ $brocker->created_at?->diffForHumans()??'-' }}</td>
					<td class="text-center">
						<a href='{{ route('brockers.show', $brocker) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<form action="{{ route('brockers.destroy', $brocker) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this brocker?') }}')">
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
			{{ $brockers->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection
