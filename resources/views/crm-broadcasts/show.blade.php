@extends('layouts.app')
@php $currentPage = 'crm-broadcasts'; @endphp
@section('title', __('Broadcast send queue'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ $broadcast->title }}</h2>
            <p class="text-body-tertiary mb-0">
                {{ __('Activities were logged for :count contacts. Open each link in WhatsApp, email, or SMS — no API send.') }}
            </p>
        </div>
        <a href="{{ route('crm-broadcasts.index') }}" class="btn btn-phoenix-secondary">{{ __('Back to list') }}</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <span class="badge {{ $broadcast->channel->badgeClass() }} me-2">{{ $broadcast->channel->label() }}</span>
            <span class="text-body-tertiary fs-9">{{ $broadcast->sent_at?->format('M j, Y H:i') }} · {{ $broadcast->sender?->full_name }}</span>
            <div class="mt-3 p-3 bg-body-highlight rounded-2">{{ $broadcast->body }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Recipients') }} ({{ $recipients->count() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover crm-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">{{ __('Contact') }}</th>
                            <th>{{ __('Phone / email') }}</th>
                            <th class="pe-4 text-end">{{ __('Open') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recipients as $row)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('contacts.show', $row['contact']) }}" class="fw-semibold">{{ $row['contact']->name }}</a>
                                </td>
                                <td class="text-body-tertiary fs-9">{{ $row['contact']->phone }} @if($row['contact']->email) · {{ $row['contact']->email }} @endif</td>
                                <td class="pe-4 text-end">
                                    @if($row['link'])
                                        <a href="{{ $row['link'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary">{{ __('Open :channel', ['channel' => $broadcast->channel->label()]) }}</a>
                                        <button type="button" class="btn btn-sm btn-phoenix-secondary" onclick="navigator.clipboard.writeText(@json($row['link']))">{{ __('Copy link') }}</button>
                                    @else
                                        <span class="text-body-tertiary fs-9">{{ __('No :channel on file', ['channel' => $broadcast->channel->label()]) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
