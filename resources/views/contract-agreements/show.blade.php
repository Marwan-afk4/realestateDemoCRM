@extends('layouts.app')
@php
	$currentPage = 'contract-agreements';
@endphp
@section('title', __('Agreement Details'))
@section('content')
<div class="container-fluid">
	<h1>{{ __('Agreement Details') }}</h1>
	<div class="mb-3">
		<a href="{{ route('contract-agreements.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Agreements')}}</a>
	</div>
	<div class="row">
		<div class="col-md-6 mb-3">
			<div class="card h-100">
				<div class="card-header">
					<h5 class="text-primary mb-0">{{ __('User Information') }}</h5>
				</div>
				<div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>{{ __('User ID') }}:</strong> {{ $agreement->user->id }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('Name') }}:</strong> 
                            <a href="{{ route('users.show', $agreement->user) }}" class="text-decoration-none">
                                {{ $agreement->user->full_name }}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('Email') }}:</strong> {{ $agreement->user->email }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('Phone') }}:</strong> {{ $agreement->user->phone ?? '-' }}
                        </li>
                    </ul>
				</div>
			</div>
		</div>

        <div class="col-md-6 mb-3">
			<div class="card h-100">
				<div class="card-header">
					<h5 class="text-info mb-0">{{ __('Contract Information') }}</h5>
				</div>
				<div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>{{ __('Contract ID') }}:</strong> {{ $agreement->contract->id }}
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('Title') }}:</strong> 
                            <a href="{{ route('contracts.show', $agreement->contract) }}" class="text-decoration-none">
                                {{ $agreement->contract->title }}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <strong>{{ __('Agreement Date') }}:</strong> {{ $agreement->created_at->format('Y-m-d H:i:s') }}
                        </li>
                    </ul>
				</div>
			</div>
		</div>

        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-secondary mb-0">{{ __('Contract Body (At Time of Viewing)') }}</h5>
                </div>
                <div class="card-body">
                    <div class="contract-body p-3 bg-light border rounded" style="white-space: pre-wrap;">
                        {{ $agreement->contract->body }}
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection
