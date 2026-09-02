@extends('layouts.app')
@php
	$currentPage = 'home-names';
@endphp
@section('title', __('Home Names'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Home Names')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		@if(\App\Models\HomeName::count() < 6)
			<a href="{{ route('home-names.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create Home Name')}} <i class="fa fa-plus"></i></a>
		@endif
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
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'name_ar', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('name_ar'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Name Ar") }}
							@if($sortField === 'name_ar')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'name_en', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('name_en'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Name En") }}
							@if($sortField === 'name_en')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
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
				@foreach($homeNames as $homeName)
				<tr>
					<td>{{ $homeName->id }}</td>
					<td>{{ $homeName->name_ar }}</td>
					<td>{{ $homeName->name_en }}</td>
					<td>{{ $homeName->created_at?->diffForHumans() }}</td>
					<td class="text-center">
						{{-- <a href='{{ route('home-names.show', $homeName) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a> --}}
						<a href='{{ route('home-names.edit', $homeName) }}' class="btn btn-subtle-warning btn-sm me-1">{{ __("Edit") }} <i class="fa fa-edit"></i></a>
						{{-- <form method='POST' action='{{ route('home-names.destroy', $homeName) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
							<input type='hidden' name='_method' value='DELETE'>
							<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
						</form> --}}
					</td>
				</tr>
				@endforeach
			</table>
			{{ $homeNames->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection