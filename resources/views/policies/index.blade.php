@extends('layouts.app')
@php
    $currentPage = 'policies';
@endphp
@section('title', __('Policy Terms & Conditions'))
@section('content')
    <div class="container-fluid">
        <h1 class="mb-3">{{ __('Policy Terms & Conditions') }}</h1>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('policies.create') }}" class="btn btn-primary btn-sm me-1">
                {{ __('Create Policy') }} <i class="fa fa-plus"></i>
            </a>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class='main-card mb-3 card'>
            <div class='card-body'>
                @if($policies->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fa fa-file-invoice mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mb-0">{{ __('No policy terms created yet.') }}</p>
                    </div>
                @else
                    <table class="mb-0 table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                        {{ __('ID') }}
                                        @if ($sortField === 'id')
                                            <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'title', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                        {{ __('Title') }}
                                        @if ($sortField === 'title')
                                            <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                        {{ __('Created At') }}
                                        @if ($sortField === 'created_at')
                                            <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($policies as $policy)
                                <tr>
                                    <td>{{ $policy->id }}</td>
                                    <td><strong>{{ $policy->title }}</strong></td>
                                    <td>{{ $policy->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href='{{ route('policies.show', $policy) }}' class="btn btn-subtle-primary btn-sm me-1">
                                            {{ __('Details') }} <i class="fa fa-eye"></i>
                                        </a>
                                        <a href='{{ route('policies.edit', $policy) }}' class="btn btn-subtle-warning btn-sm me-1">
                                            {{ __('Edit') }} <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('policies.destroy', $policy) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this policy?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-subtle-danger btn-sm">
                                                {{ __('Delete') }} <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $policies->links() }}
                @endif
            </div>
        </div>
    </div>
@endsection
