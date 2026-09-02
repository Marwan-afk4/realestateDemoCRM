@extends('layouts.app')
@php
    $currentPage = 'policies';
@endphp
@section('title', $policy->title)
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ $policy->title }}</h1>
            <div>
                <a href="{{ route('policies.edit', $policy) }}" class="btn btn-warning btn-sm me-1">
                    <i class="fa fa-edit me-1"></i> {{ __('Edit') }}
                </a>
                <a href="{{ route('policies.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="main-card mb-3 card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="text-muted mb-3" style="font-size: 0.85rem;">
                    <i class="fa fa-calendar-alt me-1"></i> {{ __('Last Updated:') }} {{ $policy->updated_at?->format('M d, Y H:i') }}
                </div>
                <hr>
                <div class="policy-content text-black mt-3" style="white-space: pre-wrap; line-height: 1.6; font-size: 1.05rem;">
                    {{ $policy->content }}
                </div>
            </div>
        </div>
    </div>
@endsection
