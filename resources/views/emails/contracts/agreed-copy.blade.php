@php
    $isRtl = app()->getLocale() === 'ar';
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
    $hasLogo = ! empty($logoPath) && is_file($logoPath);
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $contract->title }}</title>
    <style>
        body { font-family: dejavusans, sans-serif; color: #1b1d21; font-size: 12pt; }
        .brand { color: #3874ff; font-size: 10pt; letter-spacing: 1px; }
        h1 { margin: 6px 0 12px 0; font-size: 20pt; color: #102a43; }
        .meta { color: #52606d; font-size: 10pt; line-height: 1.7; }
        .rule { border-bottom: 3px solid #3874ff; margin: 12px 0 18px 0; }
        .page { border: 1px solid #d0d5dd; padding: 14px 16px; margin: 0 0 14px 0; }
        .page-title { color: #3874ff; font-size: 10pt; font-weight: bold; margin-bottom: 8px; }
        .page-body { white-space: pre-wrap; line-height: 1.8; font-size: 11pt; }
        .footer { margin-top: 20px; font-size: 9pt; color: #8b95a1; border-top: 1px solid #e4e7ec; padding-top: 10px; }
        .logo { width: 120px; margin-bottom: 8px; }
    </style>
</head>
<body>
    @if($hasLogo)
        <img src="{{ $logoPath }}" class="logo" alt="{{ $appName }}">
    @endif

    <div class="brand">{{ $appName }}</div>
    <h1>{{ $contract->title }}</h1>
    <div class="meta">
        @if($agreement)
            <div>{{ __('Contract agreement confirmed') }}</div>
        @endif
        <div>{{ __('Name') }}: {{ $user->full_name }}</div>
        <div>{{ __('Email') }}: {{ $user->email }}</div>
        @if($agreement)
            <div>{{ __('Agreement reference') }}: #{{ $agreement->id }}</div>
            <div>{{ __('Agreement date') }}: {{ $agreedAt->format('Y-m-d H:i') }}</div>
        @endif
    </div>
    <div class="rule"></div>

    @forelse($pages as $index => $pageContent)
        <div class="page">
            <div class="page-title">{{ __('Page :number', ['number' => $index + 1]) }}</div>
            <div class="page-body">{{ $pageContent }}</div>
        </div>
    @empty
        <p>{{ __('Empty') }}</p>
    @endforelse

    <div class="footer">
        @if($agreement)
            {{ __('This is an official copy of the agreement recorded in your account.') }}
            {{ __('Please keep this email for your records.') }}
        @endif
        <br>&copy; {{ now()->year }} {{ $appName }}. {{ __('All rights reserved.') }}
    </div>
</body>
</html>
