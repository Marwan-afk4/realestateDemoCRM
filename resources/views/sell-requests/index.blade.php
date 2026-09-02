@extends('layouts.app')
@php
	$currentPage = 'sell-requests';
@endphp
@section('title', __('Units Requests'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Units Requests')}}</h1>
	<div class="mb-3 d-flex justify-content-end align-items-center">
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
					<th>{{ __("User") }}</th>
					<th>{{ __("Age") }}</th>
					<th>{{ __("Location") }}</th>
					<th>{{ __("Price") }}</th>
					<th>{{ __("Unit Type") }}</th>
					<th>{{ __("Sub Type") }}</th>
					<th>{{ __("Installments") }}</th>
					<th>{{ __("Status") }}</th>
					<th>{{ __("Visibility") }}</th>
					<th>{{ __("Created At") }}</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($sellRequests as $request)
				<tr>
					<td>{{ $request->id }}</td>
					<td>
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $request->user->first_name . ' ' . $request->user->last_name}}</h6>
                                <small class="text-muted">{{ $request->user->phone }}</small>
                            </div>
                        </div>
                    </td>
					<td>{{ $request->age }}</td>
					<td>{{ $request->city }} / {{ $request->area }}</td>
					<td>{{ number_format($request->price) }}</td>
					<td>
						@if($request->uptown_type_id)
							<a href="{{ route('uptown-types.show', $request->uptown_type_id) }}">{{ $request->uptownType?->name ?? '-' }}</a>
						@else
							-
						@endif
					</td>
					<td>
						@if($request->unit_sub_type_id)
							<a href="{{ route('unit-sub-types.show', $request->unit_sub_type_id) }}">{{ $request->unitSubType?->name_en ?? '-' }}</a>
						@else
							-
						@endif
					</td>
					<td>
                        @if($request->installments)
                            <span class="badge bg-success">{{ __('Yes') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('No') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($request->status === 'contacted')
                            <span class="badge bg-success">{{ __('Contacted') }}</span>
                        @else
                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($request->visibility === 'public')
                            <span class="badge bg-primary"><i class="fa fa-globe"></i> {{ __('Public') }}</span>
                        @else
                            <span class="badge bg-secondary"><i class="fa fa-eye-slash"></i> {{ __('Private') }}</span>
                        @endif
                    </td>
					<td>{{ $request->created_at?->diffForHumans() }}</td>
					<td class="text-center">
						<a href='{{ route('sell-requests.show', $request) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<form method='POST' action='{{ route('sell-requests.destroy', $request) }}' onsubmit='return confirm("Are you sure?")' style="display:inline">
							@csrf
							@method('DELETE')
							<button type='submit' class="btn btn-subtle-danger btn-sm">{{ __('Delete') }} <i class="fa fa-trash"></i></button>
						</form>
					</td>
				</tr>
				@endforeach
			</table>
			{{ $sellRequests->links() }}
		</div>
	</div>
</div>
@endsection
