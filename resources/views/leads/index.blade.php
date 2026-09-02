@extends('layouts.app')
@php
	$currentPage = 'leads';
@endphp
@section('title', __('Leads'))
@section('content')
<div class="container-fluid">
	<h1 class="mb-3">{{__('Leads')}}</h1>
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<a href="{{ route('leads.create') }}" class="btn btn-primary btn-sm me-1">{{__('Create Lead')}} <i class="fa fa-plus"></i></a>
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
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'lead_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('lead_name'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Lead Name") }}
							@if($sortField === 'lead_name')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'lead_phone', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('lead_phone'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Lead Phone") }}
							@if($sortField === 'lead_phone')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'interested_place', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('interested_place'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Interested Place") }}
							@if($sortField === 'interested_place')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					{{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'marketing_agency_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('marketing_agency_id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Marketing Agency") }}
							@if($sortField === 'marketing_agency_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'uptown_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('uptown_id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Uptown") }}
							@if($sortField === 'uptown_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'brocker_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('brocker_id'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Brocker") }}
							@if($sortField === 'brocker_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					{{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'brocker_start_date', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('brocker_start_date'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Brocker Start Date") }}
							@if($sortField === 'brocker_start_date')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'sales_man_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('sales_man_name'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Sales Man Name") }}
							@if($sortField === 'sales_man_name')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					{{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'sales_man_phone', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('sales_man_phone'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Sales Man Phone") }}
							@if($sortField === 'sales_man_phone')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('status'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Status") }}
							@if($sortField === 'status')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					{{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'brocker_end_date', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('brocker_end_date'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Brocker End Date") }}
							@if($sortField === 'brocker_end_date')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
					<th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if(request()->filled('created_at'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Created At") }}
							@if($sortField === 'created_at')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th>
					<th class="text-center">{{ __('Actions') }}</th>
				</tr>
				@foreach($leads as $lead)
				<tr>
					<td>{{ $lead->id }}</td>
					<td><strong>{{ $lead->lead_name }}</strong></td>
					<td><strong>{{ $lead->lead_phone }}</strong></td>
					<td><strong>{{ $lead->interested_place }}</strong></td>
					{{-- <td>
						@if ($lead->marketing_agency)
							<a href='{{ route('marketing-agencies.show', $lead->marketing_agency) }}'>
								{{$lead->marketing_agency?->name ?? '' }}
							</a>
						@endif
					</td> --}}
					<td>
						@if ($lead->uptown)
							<a href='{{ route('uptowns.show', $lead->uptown) }}'>
								{{$lead->uptown?->name ?? '__' }}
							</a>
						@else
							__
						@endif
					</td>
					<td>
						@if ($lead->brocker)
							<a href='{{ route('brockers.show', $lead->brocker) }}'>
								{{$lead->brocker?->user->first_name ?? '__' }}
							</a>
						@else
							__
						@endif
					</td>
					{{-- <td>{{ $lead->brocker_start_date ?? '-' }}</td> --}}
					<td>{{ $lead->sales_man_name ?? '__' }}</td>
					{{-- <td>{{ $lead->sales_man_phone }}</td> --}}
					<td>{!! $lead->status->badge() !!}</td>
					{{-- <td>{{ $lead->brocker_end_date }}</td> --}}
					<td>{{ $lead->created_at?->diffForHumans()??'-' }}</td>
					<td class="text-center">
						<a href='{{ route('leads.show', $lead) }}' class="btn btn-subtle-primary btn-sm me-1">{{ __("Details") }} <i class="fa fa-eye"></i></a>
						<a href='{{ route('leads.edit', $lead) }}' class="btn btn-subtle-warning btn-sm me-1">{{ __("Edit") }} <i class="fa fa-edit"></i></a>
						{{-- <form method='POST' action='{{ route('leads.destroy', $lead) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
							<input type='hidden' name='_method' value='DELETE'>
							<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
						</form> --}}
					</td>
				</tr>
				@endforeach
			</table>
			{{ $leads->links('pagination::custom') }}
		</div>
	</div>
</div>
@endsection