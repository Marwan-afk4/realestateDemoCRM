@extends('layouts.app')
@php $currentPage = 'crm-broadcasts'; @endphp
@section('title', __('New broadcast'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('New broadcast') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Logged on each matching contact timeline. Open WhatsApp or email from the contact card.') }}</p>
        </div>
        <a href="{{ route('crm-broadcasts.index') }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('crm-broadcasts.store') }}">
                        @csrf
                        <x-form-input name="title" type="text" label="{{ __('Title') }}" required />
                        <x-form-select name="channel" label="{{ __('Channel') }}" :options="$channels" required />
                        <div class="row">
                            <div class="col-md-6">
                                <x-form-select name="stage" label="{{ __('Filter: pipeline stage') }}" :options="$stages" />
                            </div>
                            <div class="col-md-6">
                                <x-form-select name="source" label="{{ __('Filter: source') }}" :options="\App\Enums\ContactSource::labels()" />
                            </div>
                        </div>
                        <x-form-textarea name="body" label="{{ __('Body') }}" required />
                        <button class="btn btn-primary">{{ __('Send to matching contacts') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-body-highlight">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('How this works') }}</h5>
                    <p class="fs-9 text-body-secondary mb-0">{{ __('A broadcast writes the same note onto every matching contact. It does not send WhatsApp or email by itself.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
