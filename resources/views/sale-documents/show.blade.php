@extends('layouts.app')
@php $currentPage = 'deals'; @endphp
@section('title', $document->title)
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('deals.show', $document->deal_id) }}" class="btn btn-phoenix-secondary btn-sm">{{ __('Back to deal') }}</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()">{{ __('Print') }}</button>
    </div>
    <div class="card">
        <div class="card-body p-5">
            {!! $document->body !!}
        </div>
    </div>
</div>
@endsection
