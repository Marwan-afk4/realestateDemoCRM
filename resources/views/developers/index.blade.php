@extends('layouts.app')

@php
    $currentPage = 'developers';
@endphp

@section('title', __('Developers'))

@push('styles')
    <style>
        .developer-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .developer-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="row mb-5">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold mb-1">{{ __('Developers') }}</h1>
                    <p class="text-muted mb-0">{{ __('Mange developers, performance, and statistics.') }}</p>
                </div>
                <a href="{{ route('developers.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> {{ __('New Developer') }}
                </a>
            </div>
        </div>

        {{-- Grid --}}
        <div class="row g-4">
            @forelse($developers as $developer)
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                    <div class="card developer-card h-100 border-0 rounded-3 shadow-sm">

                        {{-- Image / Banner --}}
                        <div class="position-relative">
                            @if ($developer->image)
                                <img src="{{ $developer->image_url }}" alt="{{ $developer->name }}"
                                    class="w-100 rounded-top" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                    style="height: 180px;">
                                    <i class="fas fa-building fa-3x text-secondary"></i>
                                </div>
                            @endif

                            <div class="position-absolute top-0 end-0 m-2">
                                <a href="{{ route('developers.show', $developer) }}"
                                    class="btn btn-light btn-sm rounded-circle shadow-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body d-flex flex-column">
                            {{-- Name --}}
                            <h5 class="fw-bold mb-2">{{ $developer->name }}</h5>

                            {{-- Contact Info --}}
                            @if ($developer->email)
                                <p class="mb-2" style="font-size: 15px; font-weight: 500; color: #2d3748;">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    <a href="mailto:{{ $developer->email }}">{{ $developer->email }}</a>
                                </p>
                            @endif

                            {{-- Description --}}
                            @if ($developer->description)
                                <p class="mb-3" style="font-size: 14.5px; line-height: 1.6; color: #4a5568;">
                                    <i class="fas fa-info-circle me-2 text-secondary"></i>
                                    {{ Str::limit($developer->description, 120) }}
                                </p>
                            @endif


                            {{-- Statistics --}}
                            <div class="row g-2 text-center mb-3 mt-2">
                                <div class="col-6">
                                    <div class="fw-bold">{{ $developer->units ?? 0 }}</div>
                                    <small class="text-muted">{{ __('Total Units Listed') }}</small>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold">{{ $developer->total_deals ?? 0 }}</div>
                                    <small class="text-muted">{{ __('Total Deals') }}</small>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold text-success">{{ $developer->deals_done ?? 0 }}</div>
                                    <small class="text-muted">{{ __('Deals Completed') }}</small>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold text-primary">
                                        ${{ number_format($developer->total_profit ?? 0) }}
                                    </div>
                                    <small class="text-muted">{{ __('Total Profit Generated') }}</small>
                                </div>
                            </div>

                            {{-- Activity Period --}}
                            @if ($developer->start_date || $developer->end_date)
                                <p class="small text-muted mb-3">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <strong>{{ __('Active Period:') }}</strong>
                                    {{ $developer->start_date?->format('M d, Y') ?? __('Unknown') }}
                                    –
                                    {{ $developer->end_date?->format('M d, Y') ?? __('Present') }}
                                </p>
                            @endif

                            {{-- Actions --}}
                            <div class="mt-auto d-flex gap-2">
                                <a href="{{ route('developers.edit', $developer) }}"
                                    class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-edit"></i> {{ __('Edit Details') }}
                                </a>
                                <form action="{{ route('developers.destroy', $developer) }}" method="POST"
                                    class="flex-fill"
                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this developer?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fas fa-trash"></i> {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>


                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center shadow-sm rounded-3">
                        <i class="fas fa-info-circle me-1"></i> {{ __('No developers found.') }}
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $developers->links() }}
        </div>

    </div>
@endsection
