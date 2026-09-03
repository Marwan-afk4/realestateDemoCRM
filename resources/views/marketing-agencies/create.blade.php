@extends('layouts.app')
@php $currentPage = 'marketing-agencies'; @endphp
@section('title', __('New agency'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <h2 class="mb-4">{{ __('New marketing agency') }}</h2>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('marketing-agencies.store') }}">
            @csrf
            <x-form-input name="name" label="{{ __('Name') }}" required />
            <x-form-input name="email" type="email" label="{{ __('Email') }}" />
            <x-form-input name="phone" label="{{ __('Phone') }}" />
            <x-form-input name="start_date" type="date" label="{{ __('Start date') }}" />
            <x-form-input name="end_date" type="date" label="{{ __('End date') }}" />
            <button class="btn btn-primary" type="submit">{{ __('Create') }}</button>
        </form>
    </div></div>
</div>
@endsection
