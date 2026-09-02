@extends('layouts.app')
@php
    $currentPage = 'deals';
@endphp
@section('title', __('Deals'))
@section('content')
    <div class="container-fluid">
        <h1 class="mb-3">{{ __('Deals') }}</h1>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('deals.create') }}" class="btn btn-primary btn-sm me-1">{{ __('Create Deal') }} <i
                    class="fa fa-plus"></i></a>
            <div class="search-wrapper">
                <form action="{{ route(Route::currentRouteName(), [], false) }}" method="GET" class="d-inline-block">
                    <div class="input-group">
                        @if (request('keyword'))
                            <div class="input-group-append">
                                <a class="btn btn-secondary" href="{{ route(Route::currentRouteName(), [], false) }}">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        @endif
                        <input type="text" name="keyword" class="form-control" autocomplete="off"
                            placeholder="{{ __('Keyword') }}..." value="{{ request('keyword') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Status Filters -->
        <div class="mb-3">
            <a href="{{ route('deals.index') }}"
                class="btn btn-primary btn-sm me-1 {{ request('status') ? '' : 'active' }}">
                {{ __('All') }}
            </a>

            @foreach ($statuses as $status)
                <a href="{{ route('deals.index', ['status' => $status->value]) }}"
                    class="btn btn-sm me-1 {{ request('status') === $status->value ? 'active' : '' }}"
                    style="background-color: #{{ $status->color() }}; color: #{{ $status->textColor() }}">
                    {{ $status->label() }}
                    ({{ $dealsStatusCounts[$status->value] ?? 0 }})
                </a>
            @endforeach
        </div>

        <div class='main-card mb-3 card'>
            <div class='card-body'>
                <table class="mb-0 table table-hover">
                    <tr>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('id'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Id') }}
                                @if ($sortField === 'id')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'fullname', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('fullname'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Fullname') }}
                                @if ($sortField === 'fullname')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        {{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'nationality_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if (request()->filled('nationality_id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Nationality") }}
							@if ($sortField === 'nationality_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'phone', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('phone'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Phone') }}
                                @if ($sortField === 'phone')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        {{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if (request()->filled('email'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Email") }}
							@if ($sortField === 'email')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'developer_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('developer_id'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Developer') }}
                                @if ($sortField === 'developer_id')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'compound_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('compound_id'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Compound') }}
                                @if ($sortField === 'compound_id')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'uptown_type_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('uptown_type_id'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Unit Type') }}
                                @if ($sortField === 'uptown_type_id')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'number_of_units', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('number_of_units'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Number Of Units') }}
                                @if ($sortField === 'number_of_units')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('status'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Status') }}
                                @if ($sortField === 'status')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('created_at'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Created At') }}
                                @if ($sortField === 'created_at')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                    @foreach ($deals as $deal)
                        <tr>
                            <td>{{ $deal->id }}</td>
                            <td>{{ $deal->fullname }}</td>
                            {{-- <td>{{ $deal->nationality_id }}</td> --}}
                            <td>{{ $deal->phone }}</td>
                            {{-- <td>{{ $deal->email }}</td> --}}
                            <td>
                                @if ($deal->developer)
                                    <a href='{{ route('developers.show', $deal->developer) }}'>
                                        {{ $deal->developer?->name ?? '' }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if ($deal->compound)
                                    <a href='{{ route('compounds.show', $deal->compound) }}'>
                                        {{ $deal->compound?->compound_name ?? '' }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if ($deal->uptownType)
                                    <a href='{{ route('uptown-types.show', $deal->uptownType) }}'>
                                        {{ $deal->uptownType?->name ?? '' }}
                                    </a>
                                @endif
                            </td>
                            <td>{{ $deal->number_of_units }}</td>
                            <td>{!! $deal->status->badge() !!}</td>
                            <td>{{ $deal->created_at?->diffForHumans() ?? '-' }}</td>
                            <td class="text-center">
                                <a href='{{ route('deals.show', $deal) }}'
                                    class="btn btn-subtle-primary btn-sm me-1">{{ __('Details') }} <i
                                        class="fa fa-eye"></i></a>
                                {{-- <a href='{{ route('deals.edit', $deal) }}' class="btn btn-subtle-warning btn-sm me-1">{{ __("Edit") }} <i class="fa fa-edit"></i></a> --}}
                                {{-- <form method='POST' action='{{ route('deals.destroy', $deal) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
							<input type='hidden' name='_method' value='DELETE'>
							<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
						</form> --}}
                            </td>
                        </tr>
                    @endforeach
                </table>
                {{ $deals->links('pagination::custom') }}
            </div>
        </div>
    </div>
@endsection
