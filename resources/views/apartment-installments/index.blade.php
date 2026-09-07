@extends('layouts.app')
@php
	$currentPage = 'apartment-installments';
@endphp
@section('title', __('Mortgage Requests'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Mortgage Requests')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('apartment-installments.create') }}" class="btn btn-primary btn-sm me-1">{{ __('Create Mortgage Request') }} <i class="fa fa-plus"></i></a>
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
					<th>{{ __("Apartment") }}</th>
					<th>{{ __("Monthly Income") }}</th>
					<th>{{ __("Monthly Installment") }}</th>
					<th>{{ __("Years") }}</th>
					<th>{{ __("Deposit") }}</th>
					<th>{{ __("Status") }}</th>
					<th>{{ __("Created At") }}</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($installments as $request)
				<tr>
					<td>{{ $request->id }}</td>
					<td>
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $request->user->first_name }} {{ $request->user->last_name }}</h6>
                                <small class="text-muted">{{ $request->user->phone }}</small>
                            </div>
                        </div>
                    </td>
					<td>
                        @if($request->apartment)
                            <a href="{{ route('uptowns.show', $request->apartment_id) }}">{{ $request->apartment->description }}</a>
                        @else
                            -
                        @endif
                    </td>
					<td>{{ number_format($request->monthly_income) }}</td>
					<td>{{ $request->monthly_installment ? number_format($request->monthly_installment) : '-' }}</td>
					<td>{{ $request->years_of_installment }}</td>
					<td>{{ $request->deposit_percetage }}%</td>
					<td>
                        @if($request->status === 'contacted')
                            <span class="badge bg-success">{{ __('Contacted') }}</span>
                        @else
                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                        @endif
                    </td>
					<td>{{ $request->created_at?->diffForHumans() }}</td>
					<td class="text-center">
						<a href='{{ route('apartment-installments.show', $request) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<form method='POST' action='{{ route('apartment-installments.destroy', $request) }}' onsubmit='return confirm("Are you sure?")' style="display:inline">
							@csrf
							@method('DELETE')
							<button type='submit' class="btn btn-subtle-danger btn-sm">{{ __('Delete') }} <i class="fa fa-trash"></i></button>
						</form>
					</td>
				</tr>
				@endforeach
			</table>
			{{ $installments->links() }}
		</div>
	</div>
</div>
@endsection
