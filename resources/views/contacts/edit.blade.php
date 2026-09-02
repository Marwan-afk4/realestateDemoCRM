@extends('layouts.app')
@php $currentPage = 'contacts'; @endphp
@section('title', __('Edit Contact'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Edit Contact') }}</h2>
            <p class="text-body-tertiary mb-0">{{ $contact->name }}</p>
        </div>
        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
    </div>
    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('contacts.update', $contact) }}">
                @csrf
                @method('PUT')
                @include('contacts._form')
                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">{{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
