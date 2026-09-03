@extends('layouts.app')
@php $currentPage = 'marketing-agencies'; @endphp
@section('title', __('Edit agency'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <h2 class="mb-4">{{ __('Edit') }} — {{ $agency->name }}</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('marketing-agencies.update', $agency) }}">
            @csrf @method('PUT')
            <x-form-input name="name" label="{{ __('Name') }}" :value="$agency->name" required />
            <x-form-input name="email" type="email" label="{{ __('Email') }}" :value="$agency->email" />
            <x-form-input name="phone" label="{{ __('Phone') }}" :value="$agency->phone" />
            <x-form-input name="start_date" type="date" label="{{ __('Start date') }}" :value="$agency->start_date?->format('Y-m-d')" />
            <x-form-input name="end_date" type="date" label="{{ __('End date') }}" :value="$agency->end_date?->format('Y-m-d')" />
            <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
        </form>
    </div></div>
</div>
@endsection
