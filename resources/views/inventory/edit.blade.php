@extends('layouts.app')
@php $currentPage = 'inventory-units'; @endphp
@section('title', __('Edit unit'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <div>
            <h2 class="mb-2">{{ __('Edit unit') }} {{ $unit->code }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Stock status is changed by holds, deals, and contracts — not this form.') }}</p>
        </div>
        <a href="{{ route('inventory-units.show', $unit) }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('inventory-units.update', $unit) }}">
                @csrf @method('PUT')
                @include('inventory._form')
                <button class="btn btn-primary">{{ __('Save Changes') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
