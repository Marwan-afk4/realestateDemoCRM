@extends('layouts.app')
@php $currentPage = 'inventory-units'; @endphp
@section('title', __('Add physical unit'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <div>
            <h2 class="mb-2">{{ __('Add physical unit') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('This is stock, not a listing card.') }}</p>
        </div>
        <a href="{{ route('inventory-units.index') }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('inventory-units.store') }}">
                @csrf
                @include('inventory._form')
                <button class="btn btn-primary">{{ __('Save unit') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
