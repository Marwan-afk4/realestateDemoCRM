@extends('layouts.app')
@php
	$currentPage = 'uptown-types';
@endphp
@section('title', __('Uptown Types'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Uptown Types')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('uptown-types.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create Uptown Type')}} <i class="fa fa-plus"></i></a>
		<div class="search-wrapper">
			<form action="{{ route(Route::currentRouteName(), [], false) }}" method="GET">
				<div class="input-group">
					@if(request()->query())
						<a class="btn btn-secondary" href="{{ route(Route::currentRouteName(), [], false) }}">
							<i class="fa fa-times"></i>
						</a>
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
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('name'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Name") }}
							@if($sortField === 'name')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('status'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Status") }}
							@if($sortField === 'status')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
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
				@foreach($uptownTypes as $uptownType)
				<tr>
					<td>{{ $uptownType->id }}</td>
					<td>{{ $uptownType->name }}</td>
					<td>{!! $uptownType->status->badge() !!}</td>
					<td>{{ $uptownType->created_at?->diffForHumans()??'-' }}</td>
					<td class="text-center">
						<a href='{{ route('uptown-types.show', $uptownType) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						{{-- <form method='POST' action='{{ route('uptown-types.destroy', $uptownType) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
							<input type='hidden' name='_method' value='DELETE'>
							<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
						</form> --}}
					</td>
				</tr>
				@endforeach
			</table>
			{{ $uptownTypes->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection
