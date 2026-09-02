@extends('layouts.app')
@php $currentPage = 'crm-broadcasts'; @endphp
@section('title', __('Broadcasts'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Broadcasts') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Log one message to a filtered contact list.') }}</p>
        </div>
        <a href="{{ route('crm-broadcasts.create') }}" class="btn btn-primary">
            <span class="fas fa-plus me-2"></span>{{ __('New broadcast') }}
        </a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover crm-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">{{ __('Title') }}</th>
                            <th>{{ __('Channel') }}</th>
                            <th>{{ __('Recipients') }}</th>
                            <th class="pe-4">{{ __('Sent') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($broadcasts as $broadcast)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ $broadcast->title }}</div>
                                    <div class="fs-10 text-body-tertiary text-truncate" style="max-width:28rem;">{{ $broadcast->body }}</div>
                                </td>
                                <td><span class="badge {{ $broadcast->channel->badgeClass() }}">{{ $broadcast->channel->label() }}</span></td>
                                <td><span class="badge badge-phoenix badge-phoenix-primary">{{ $broadcast->recipient_count }}</span></td>
                                <td class="pe-4 text-body-tertiary">{{ $broadcast->sent_at?->diffForHumans() }} · {{ $broadcast->sender?->full_name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6"><div class="crm-empty">{{ __('No broadcasts yet.') }}</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($broadcasts->hasPages())
            <div class="card-footer">{{ $broadcasts->links() }}</div>
        @endif
    </div>
</div>
@endsection
