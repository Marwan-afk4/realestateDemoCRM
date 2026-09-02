@extends('layouts.app')
@php
    $currentPage = 'push-notifications';
@endphp
@section('title', $notification->title)
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ __('Notification Details') }}</h1>
            <a href="{{ route('push-notifications.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="main-card mb-3 card border-0 shadow-sm">
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-3">{{ __('Title') }}</dt>
                    <dd class="col-sm-9">{{ $notification->title }}</dd>

                    <dt class="col-sm-3">{{ __('Message') }}</dt>
                    <dd class="col-sm-9" style="white-space: pre-wrap;">{{ $notification->body }}</dd>

                    <dt class="col-sm-3">{{ __('Audience') }}</dt>
                    <dd class="col-sm-9">
                        @if ($notification->audience === 'all')
                            {{ __('All users') }}
                        @else
                            {{ __('Selected users') }}
                        @endif
                    </dd>

                    <dt class="col-sm-3">{{ __('Delivered') }}</dt>
                    <dd class="col-sm-9">{{ $notification->sent_count }}</dd>

                    <dt class="col-sm-3">{{ __('Failed') }}</dt>
                    <dd class="col-sm-9">{{ $notification->failed_count }}</dd>

                    <dt class="col-sm-3">{{ __('Sent By') }}</dt>
                    <dd class="col-sm-9">{{ $notification->sender?->full_name ?? '—' }}</dd>

                    <dt class="col-sm-3">{{ __('Sent At') }}</dt>
                    <dd class="col-sm-9">{{ $notification->created_at?->format('Y-m-d H:i') ?? '—' }}</dd>
                </dl>

                @if ($recipients->isNotEmpty())
                    <hr>
                    <h5 class="mb-3">{{ __('Recipients') }}</h5>
                    <ul class="mb-0">
                        @foreach ($recipients as $user)
                            <li>{{ $user->full_name }} — {{ $user->phone ?: $user->email }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
