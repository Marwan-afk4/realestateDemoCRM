@extends('layouts.app')
@php
	$currentPage = 'residential-data';
@endphp
@section('title', __('Residential Data Fields'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Residential Data Fields')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('residential-data.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create Field')}} <i class="fa fa-plus"></i></a>
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
					<th>{{ __("Field Name") }}</th>
					<th>{{ __("Label (EN)") }}</th>
					<th>{{ __("Type") }}</th>
					<th>{{ __("Required") }}</th>
					<th>{{ __("Status") }}</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($fields as $field)
				<tr>
					<td>{{ $field->id }}</td>
					<td>{{ $field->field_name }}</td>
					<td>{{ $field->label_en }}</td>
					<td>{{ $field->type }}</td>
					<td>{{ $field->is_required ? __('Yes') : __('No') }}</td>
					<td>{{ ucfirst($field->status) }}</td>
					<td class="text-center">
						<a href='{{ route('residential-data.edit', $field) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Edit") }} <i class="fa fa-edit"></i></a>
						<form method='POST' action='{{ route('residential-data.destroy', $field) }}' onsubmit='return confirm("Are you sure?")' style="display:inline">
							@csrf
							@method('DELETE')
							<button type='submit' class="btn btn-subtle-danger btn-sm">{{ __('Delete') }} <i class="fa fa-trash"></i></button>
						</form>
					</td>
				</tr>
				@endforeach
			</table>
			{{ $fields->links() }}
		</div>
	</div>
</div>
@endsection
