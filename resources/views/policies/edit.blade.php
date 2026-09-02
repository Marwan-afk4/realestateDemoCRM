@extends('layouts.app')
@php
    $currentPage = 'policies';
@endphp
@section('title', __('Edit Policy'))
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ __('Edit Policy Terms & Conditions') }}</h1>
            <a href="{{ route('policies.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
            </a>
        </div>

        <div class='main-card mb-3 card border-0 shadow-sm'>
            <div class='card-body p-4'>
                <form action="{{ route('policies.update', $policy) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">{{ __('Policy Title') }}</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $policy->title) }}" placeholder="e.g. Terms of Service, Privacy Policy" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label fw-bold">{{ __('Policy Content') }}</label>
                        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" 
                                  rows="15" placeholder="{{ __('Write your policy terms and conditions here...') }}" required>{{ old('content', $policy->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-save me-1"></i> {{ __('Update Policy') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
