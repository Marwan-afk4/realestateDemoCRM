@extends('layouts.app')
@php
	$currentPage = 'home-names';
@endphp
@section('title', $homeName->name)
@section('content')
<div class="container-fluid">
	<h1>{{ $homeName->name }}</h1>
	<div class="mb-3">
		<a href="{{ route('home-names.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-right"></i> {{__('Back to')}} {{__('Home Names')}}</a>
		<a href='{{ route('home-names.edit', $homeName) }}' class="btn btn-warning btn-sm me-1">{{ __('Edit') }} <i class="fa fa-edit"></i></a>
	</div>
	<div class="card">
		<div class="card-body">
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<strong>{{ __("Id") }}:</strong> {{ $homeName->id }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Name Ar") }}:</strong> {{ $homeName->name_ar }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Name En") }}:</strong> {{ $homeName->name_en }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Created At") }}:</strong> {{ $homeName->created_at }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Updated At") }}:</strong> {{ $homeName->updated_at }}
				</li>
			</ul>
		</div>
	</div>
	<div class="mt-3">
		{{-- <form method='POST' action='{{ route('home-names.destroy', $homeName) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
			<input type='hidden' name='_method' value='DELETE'>
			<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
		</form> --}}
	</div>
</div>
@endsection