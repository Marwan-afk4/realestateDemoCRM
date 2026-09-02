@extends('layouts.app')
@php
	$currentPage = 'contracts';
@endphp
@section('title', __('Contracts'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Contracts')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create Contract')}} <i class="fa fa-plus"></i></a>
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
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('title'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Title") }}
							@if($sortField === 'title')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>{{ __("Pages") }}</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('created_at'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Created At") }}
							@if($sortField === 'created_at')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($contracts as $contract)
				<tr>
					<td>{{ $contract->id }}</td>
					<td>{{ $contract->title }}</td>
					<td>
                        <span class="badge bg-subtle-primary text-primary me-2">
                            {{ count(is_array($contract->pages) ? $contract->pages : [1]) }} {{ __('Pages') }}
                        </span>
                        <span class="text-muted small">
                            {{ Str::limit(is_array($contract->pages) && isset($contract->pages[0]) ? $contract->pages[0] : ($contract->body ?? ''), 40) }}
                        </span>
                    </td>
					<td>{{ $contract->created_at?->diffForHumans()??'-' }}</td>
					<td class="text-center">
						<a href='{{ route('contracts.show', $contract) }}' class="btn btn-subtle-info btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<a href='{{ route('contracts.edit', $contract) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Edit") }} <i class="fa fa-edit"></i></a>
						<form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('{{ __('Are you sure you want to delete this contract?') }}')">
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
			{{ $contracts->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection
