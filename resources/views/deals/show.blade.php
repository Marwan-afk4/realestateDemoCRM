@extends('layouts.app')
@php
    $currentPage = 'deals';
@endphp
@section('title', $deal->name)
@section('content')
    <div class="container-fluid">
        <h1>{{ $deal->fullname }}</h1>
        <div class="mb-3">
            <a href="{{ route('deals.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i>
                {{ __('Back to') }} {{ __('Deals') }}</a>
            <a href='{{ route('deals.edit', $deal) }}' class="btn btn-warning btn-sm me-1">{{ __('Edit') }} <i
                    class="fa fa-edit"></i></a>
        </div>
        <div class="card">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>{{ __('Id') }}:</strong> {{ $deal->id }}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Fullname') }}:</strong> {{ $deal->fullname }}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Nationality Id') }}:</strong> {{ $deal->nationality_id }}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Phone') }}:</strong> {{ $deal->phone }}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Email') }}:</strong> <a
                            href="mailto:{{ $deal->email }}">{{ $deal->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Developer') }}:</strong>
                        @if ($deal->developer)
                            <a href='{{ route('developers.show', $deal->developer) }}'>
                                {{ $deal->developer?->name ?? '' }}
                            </a>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Compound') }}:</strong>
                        @if ($deal->compound)
                            <a href='{{ route('compounds.show', $deal->compound) }}'>
                                {{ $deal->compound?->compound_name ?? '' }}
                            </a>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Unit Type') }}:</strong>
                        @if ($deal->uptownType)
                            <a href='{{ route('uptown-types.show', $deal->uptownType) }}'>
                                {{ $deal->uptownType?->name ?? '' }}
                            </a>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Number Of Units') }}:</strong> {{ $deal->number_of_units }}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Status') }}:</strong> {!! $deal->status->badge() !!}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Created At') }}:</strong> {{ $deal->created_at?->diffForHumans() ?? '-' }}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ __('Updated At') }}:</strong> {{ $deal->updated_at?->diffForHumans() ?? '-' }}
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-3">
            {{-- <form method='POST' action='{{ route('deals.destroy', $deal) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
			<input type='hidden' name='_method' value='DELETE'>
			<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
		</form> --}}
        </div>
    </div>
@endsection
