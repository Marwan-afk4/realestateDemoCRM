@extends('layouts.app')
@php
    $currentPage = 'push-notifications';
@endphp
@section('title', __('Send Push Notification'))
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>{{ __('Send Push Notification') }}</h1>
            <a href="{{ route('push-notifications.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="main-card mb-3 card border-0 shadow-sm">
            <div class="card-body p-4">
                <p class="text-body-secondary mb-4">
                    {{ __('This message will appear as a push notification on users’ phones that have the app installed and notifications enabled.') }}
                    {{ trans_choice(':count device is currently registered.|:count devices are currently registered.', $deviceCount, ['count' => $deviceCount]) }}
                </p>

                <form action="{{ route('push-notifications.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">{{ __('Title') }}</label>
                        <input type="text" name="title" id="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" maxlength="255" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label fw-bold">{{ __('Message') }}</label>
                        <textarea name="body" id="body" rows="5"
                            class="form-control @error('body') is-invalid @enderror"
                            maxlength="1000" required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Send to') }}</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="form-check">
                                <input class="form-check-input" type="radio" name="audience" id="audience_all"
                                    value="all" {{ old('audience', 'all') === 'all' ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('All users with a registered device') }}</span>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="radio" name="audience" id="audience_selected"
                                    value="selected" {{ old('audience') === 'selected' ? 'checked' : '' }}>
                                <span class="form-check-label">{{ __('Selected users') }}</span>
                            </label>
                        </div>
                        @error('audience')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="user-picker" class="mb-4 {{ old('audience') === 'selected' ? '' : 'd-none' }}">
                        <label class="form-label fw-bold">{{ __('Users') }}</label>
                        @if ($users->isEmpty())
                            <div class="alert alert-warning mb-0">
                                {{ __('No users have registered a device token yet. They must open the mobile app while logged in.') }}
                            </div>
                        @else
                            <input type="search" id="user-search" class="form-control mb-2"
                                placeholder="{{ __('Search by name, email or phone...') }}">
                            <div class="border rounded p-2" style="max-height: 280px; overflow-y: auto;">
                                @foreach ($users as $user)
                                    <label class="d-flex align-items-center gap-2 py-1 user-row"
                                        data-search="{{ strtolower($user->full_name.' '.$user->email.' '.$user->phone) }}">
                                        <input type="checkbox" class="form-check-input mt-0" name="user_ids[]"
                                            value="{{ $user->id }}"
                                            {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}>
                                        <span>
                                            {{ $user->full_name }}
                                            <span class="text-body-secondary small">
                                                {{ $user->phone ?: $user->email }}
                                                · {{ trans_choice(':count device|:count devices', $user->device_tokens_count, ['count' => $user->device_tokens_count]) }}
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('user_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary px-4" {{ $deviceCount === 0 ? 'disabled' : '' }}>
                        <i class="fa fa-paper-plane me-1"></i> {{ __('Send Notification') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allRadio = document.getElementById('audience_all');
            const selectedRadio = document.getElementById('audience_selected');
            const picker = document.getElementById('user-picker');
            const search = document.getElementById('user-search');

            function togglePicker() {
                picker.classList.toggle('d-none', !selectedRadio.checked);
            }

            allRadio.addEventListener('change', togglePicker);
            selectedRadio.addEventListener('change', togglePicker);

            if (search) {
                search.addEventListener('input', function() {
                    const term = this.value.toLowerCase();
                    document.querySelectorAll('.user-row').forEach(function(row) {
                        row.style.display = row.dataset.search.includes(term) ? '' : 'none';
                    });
                });
            }
        });
    </script>
@endpush
