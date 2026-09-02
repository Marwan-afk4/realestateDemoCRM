@extends('layouts.app')
@php
    $currentPage = 'push-notifications';
@endphp
@section('title', __('Push Notifications'))
@section('content')
    <div class="container-fluid">
        <h1 class="mb-3">{{ __('Push Notifications') }}</h1>
        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('push-notifications.create') }}" class="btn btn-primary btn-sm">
                {{ __('Send Notification') }} <i class="fa fa-paper-plane ms-1"></i>
            </a>
            <span class="text-body-secondary small">
                <i class="fa fa-mobile-alt me-1"></i>
                {{ trans_choice(':count registered device|:count registered devices', $deviceCount, ['count' => $deviceCount]) }}
            </span>
        </div>

        <div class="main-card mb-3 card">
            <div class="card-body">
                @if ($notifications->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fa fa-bell mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mb-0">{{ __('No push notifications sent yet.') }}</p>
                    </div>
                @else
                    <table class="mb-0 table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                        {{ __('ID') }}
                                        @if ($sortField === 'id')
                                            <i class="text-primary">{{ strtolower($sortOrder) === 'asc' ? '▼' : '▲' }}</i>
                                        @endif
                                    </a>
                                </th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Audience') }}</th>
                                <th>{{ __('Sent') }}</th>
                                <th>{{ __('Failed') }}</th>
                                <th>{{ __('Sent By') }}</th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                        {{ __('Sent At') }}
                                        @if ($sortField === 'created_at')
                                            <i class="text-primary">{{ strtolower($sortOrder) === 'asc' ? '▼' : '▲' }}</i>
                                        @endif
                                    </a>
                                </th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->id }}</td>
                                    <td>
                                        <strong>{{ $notification->title }}</strong>
                                        <div class="small text-body-secondary text-truncate" style="max-width: 320px;">
                                            {{ $notification->body }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($notification->audience === 'all')
                                            <span class="badge bg-primary">{{ __('All users') }}</span>
                                        @else
                                            <span class="badge bg-info">{{ __('Selected users') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->sent_count }}</td>
                                    <td>
                                        @if ($notification->failed_count > 0)
                                            <span class="text-danger">{{ $notification->failed_count }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>{{ $notification->sender?->full_name ?? '—' }}</td>
                                    <td>{{ $notification->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('push-notifications.show', $notification) }}"
                                            class="btn btn-subtle-primary btn-sm me-1">
                                            {{ __('Details') }} <i class="fa fa-eye"></i>
                                        </a>
                                        <form action="{{ route('push-notifications.destroy', $notification) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('{{ __('Delete this notification record?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-subtle-danger btn-sm">
                                                {{ __('Delete') }} <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $notifications->links('pagination::custom') }}
                @endif
            </div>
        </div>
    </div>
@endsection
