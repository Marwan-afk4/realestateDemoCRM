@extends('layouts.app')
@php
	$currentPage = 'users';
@endphp
@section('title', __('Users'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Users')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('users.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create User')}} <i class="fa fa-plus"></i></a>
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
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'full_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('full_name'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Full Name") }}
							@if($sortField === 'full_name')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('email'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Email") }}
							@if($sortField === 'email')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'phone', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('phone'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Phone") }}
							@if($sortField === 'phone')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
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
				@foreach($users as $user)
				<tr>
					<td>{{ $user->id }}</td>
					<td>{{ $user->full_name }}</td>
					<td>
                        <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                    </td>
					<td>{{ $user->phone }}</td>
					<td>{!! $user->status->badge() !!}</td>
					<td>{{ $user->created_at?->diffForHumans()??'-' }}</td>
					<td class="text-center">
						<a href='{{ route('users.show', $user) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')">
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
			{{ $users->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection
