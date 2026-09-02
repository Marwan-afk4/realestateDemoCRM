@extends('layouts.app')
@php
	$currentPage = 'unit-sub-types';
@endphp
@section('title', __('Unit Sub-Types'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Unit Sub-Types')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('unit-sub-types.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create Sub-Type')}} <i class="fa fa-plus"></i></a>
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
					<th>{{ __("Id") }}</th>
					<th>{{ __("Parent Type") }}</th>
					<th>{{ __("Name (EN)") }}</th>
					<th>{{ __("Name (AR)") }}</th>
					<th>{{ __("Created At") }}</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($subTypes as $subType)
				<tr>
					<td>{{ $subType->id }}</td>
					<td>{{ $subType->uptownType->name }}</td>
					<td>{{ $subType->name_en }}</td>
					<td>{{ $subType->name_ar }}</td>
					<td>{{ $subType->created_at?->diffForHumans()??'-' }}</td>
					<td class="text-center">
						<a href='{{ route('unit-sub-types.edit', $subType) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Edit") }} <i class="fa fa-edit"></i></a>
						<form method='POST' action='{{ route('unit-sub-types.destroy', $subType) }}' style="display:inline" onsubmit='return confirm("Are you sure you want to delete this item?")'>
							@csrf
                            @method('DELETE')
							<button type='submit' class="btn btn-subtle-danger btn-sm">{{ __('Delete') }} <i class="fa fa-trash"></i></button>
						</form>
					</td>
				</tr>
				@endforeach
			</table>
			{{ $subTypes->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection
