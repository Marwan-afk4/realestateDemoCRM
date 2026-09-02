@extends('layouts.app')
@php
	$currentPage = 'contract-agreements';
@endphp
@section('title', __('Contract Agreements'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Contract Agreements')}}</h1>
	<div class="mb-3 d-flex justify-content-end align-items-center">
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
                    <input type="text" name="keyword" class="form-control" autocomplete="off" placeholder="{{ __('Search...') }}" value="{{ request('keyword') }}">
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
					<th>{{ __("Contract") }}</th>
					<th>{{ __("User") }}</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('created_at'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Agreement Date") }}
							@if($sortField === 'created_at')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($agreements as $agreement)
				<tr>
					<td>{{ $agreement->id }}</td>
					<td>
                        <a href="{{ route('contracts.show', $agreement->contract) }}" class="text-decoration-none">
                            {{ Str::limit($agreement->contract->title, 30) }}
                        </a>
                    </td>
					<td>
                        <a href="{{ route('users.show', $agreement->user) }}" class="text-decoration-none">
                            {{ $agreement->user->full_name }}
                        </a>
                        <div class="text-muted small">{{ $agreement->user->email }}</div>
                    </td>
					<td>{{ $agreement->created_at->format('Y-m-d H:i') }}</td>
					<td class="text-center">
						<a href='{{ route('contract-agreements.show', $agreement->id) }}' class="btn btn-subtle-info btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<form action="{{ route('contract-agreements.destroy', $agreement->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this agreement?') }}')">
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
			{{ $agreements->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection
