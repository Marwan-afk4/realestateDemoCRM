@extends('layouts.app')
@php $currentPage = 'message-templates'; @endphp
@section('title', __('Create Template'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Create Template') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Use name and agent as placeholders in the body.') }}</p>
        </div>
        <a href="{{ route('message-templates.index') }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
    </div>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('message-templates.store') }}">
                        @csrf
                        <x-form-input name="name" type="text" label="{{ __('Name') }}" required />
                        <x-form-select name="channel" label="{{ __('Channel') }}" :options="$channels" required />
                        <x-form-input name="subject" type="text" label="{{ __('Subject') }}" />
                        <x-form-textarea name="body" label="{{ __('Body. Use name and agent placeholders.') }}" required />
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="is_active">
                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        </div>
                        <button class="btn btn-primary">{{ __('Add') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-body-highlight">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Placeholders') }}</h5>
                    <p class="fs-9 text-body-tertiary mb-2">{{ __('These are replaced when you send from a contact card.') }}</p>
                    <code class="d-block mb-1">@{{name}}</code>
                    <code class="d-block">@{{agent}}</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
