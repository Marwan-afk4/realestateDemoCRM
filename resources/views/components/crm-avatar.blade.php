@props(['contact' => null, 'size' => 'xl', 'initials' => null, 'color' => null])
@php
    $map = ['s' => 'avatar-s', 'm' => 'avatar-m', 'l' => 'avatar-l', 'xl' => 'avatar-xl', '2xl' => 'avatar-2xl', '5xl' => 'avatar-5xl'];
    $letters = $initials ?? $contact?->initials() ?? '?';
    $hex = $color ?? $contact?->avatarColor() ?? '3874ff';
@endphp
<div {{ $attributes->class(['avatar', $map[$size] ?? 'avatar-xl']) }}>
    <div class="avatar-name rounded-circle" style="background:#{{ $hex }};color:#fff;">
        <span style="color:#fff;">{{ $letters }}</span>
    </div>
</div>
