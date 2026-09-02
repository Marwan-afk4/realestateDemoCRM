@extends('layouts.app')

@php
    $currentPage = 'requests';
@endphp

@section('title', __('Complaints Management'))

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold mb-1 text-dark">{{ __('Complaints Management') }}</h1>
                    <p class="text-muted mb-0">{{ __('Manage user complaints and messages') }}</p>
                </div>
                <div>
                    @php
                        $totalPending = \App\Models\Complaint::where('status', 'open')->count();
                    @endphp
                    @if($totalPending > 0)
                        <div class="alert alert-warning d-flex align-items-center gap-2 mb-0 py-2 px-3 shadow-sm rounded-pill">
                            <i class="fas fa-clock"></i>
                            <span><strong>{{ $totalPending }}</strong> {{ __('items need attention') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Layout --}}
    <div class="row">
        {{-- Content --}}
        <div class="col-12">
            {{-- Content Header --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-comment-dots text-warning me-2"></i>{{ __('Complaints') }}
                    </h4>

                    {{-- Search --}}
                    <form action="{{ route('requests.index') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="complaints">
                        <input type="text" name="keyword"
                               class="form-control rounded-pill px-3"
                               placeholder="{{ __('Search complaints...') }}"
                               value="{{ request('keyword') }}">
                        @if (request('keyword'))
                            <a class="btn btn-light rounded-pill shadow-sm" href="{{ route('requests.index', ['tab' => 'complaints']) }}">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary rounded-pill shadow-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Content Body --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    @include('requests.partials.complaints')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
