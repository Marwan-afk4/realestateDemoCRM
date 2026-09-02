@extends('layouts.app')
@php $currentPage = 'contacts'; @endphp
@section('title', __('Create Contact'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Create Contact') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Duplicate phones are blocked. One person, many tickets.') }}</p>
        </div>
        <a href="{{ route('contacts.index') }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('contacts.store') }}">
                @csrf
                @include('contacts._form')
                <div class="form-check mb-3 mt-2">
                    <input class="form-check-input" type="checkbox" name="add_to_pipeline" value="1" id="add_to_pipeline" checked>
                    <label class="form-check-label" for="add_to_pipeline">{{ __('Add to pipeline') }}</label>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">{{ __('Add contact') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
