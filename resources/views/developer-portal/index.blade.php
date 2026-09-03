@extends('layouts.app')
@php $currentPage = 'developer-portal'; @endphp
@section('title', __('Developer portal'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ $developer->name_en ?? $developer->name_ar }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Live stock, deals, and authorized brokers for this developer.') }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('developer-portal.inventory') }}" class="btn btn-phoenix-secondary">{{ __('Inventory') }}</a>
            <a href="{{ route('developer-portal.brokers') }}" class="btn btn-phoenix-secondary">{{ __('Brokers') }}</a>
        </div>
    </div>
    <div class="row g-3">
        @foreach ([__('Compounds') => $stats['compounds'], __('Available') => $stats['available'], __('Reserved') => $stats['reserved'], __('Sold / handed') => $stats['sold'], __('Deals') => $stats['deals'], __('Authorized brokers') => $stats['brokers']] as $label => $value)
            <div class="col-md-4 col-lg-2">
                <div class="card h-100"><div class="card-body"><div class="fs-10 text-body-tertiary">{{ $label }}</div><div class="fs-4 fw-bold">{{ $value }}</div></div></div>
            </div>
        @endforeach
    </div>
</div>
@endsection
