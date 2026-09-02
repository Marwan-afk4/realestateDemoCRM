@extends('layouts.app')
@php
	$currentPage = 'uptown-types';
@endphp
@section('title', $uptownType->name)
@section('content')
<div class="container-fluid">
	<h1>{{ $uptownType->name }}</h1>
	<div class="mb-3">
		<a href="{{ route('uptown-types.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Uptown Types')}}</a>
		<a href='{{ route('uptown-types.edit', $uptownType) }}' class="btn btn-warning btn-sm me-1">{{ __('Edit') }} <i class="fa fa-edit"></i></a>
		{{-- <a href="{{ route('uptown-types.uptowns', $uptownType) }}" class="list-group-item">{{ __('uptown') }}</a> --}}
	</div>
	<div class="card">
		<div class="card-body">
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<strong>{{ __("Id") }}:</strong> {{ $uptownType->id }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Name") }}:</strong> {{ $uptownType->name }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Status") }}:</strong> {!! $uptownType->status->badge() !!}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Created At") }}:</strong> {{ $uptownType->created_at?->diffForHumans() ?? '-' }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Updated At") }}:</strong> {{ $uptownType->updated_at?->diffForHumans() ?? '-' }}
				</li>
			</ul>
		</div>
	</div>
	<div class="mt-3">
		{{-- <form method='POST' action='{{ route('uptown-types.destroy', $uptownType) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
			<input type='hidden' name='_method' value='DELETE'>
			<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
		</form> --}}
	</div>
</div>
@endsection
