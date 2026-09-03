@extends('layouts.app')
@php $currentPage = 'after-sales'; @endphp
@section('title', __('After-sales'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ __('After-sales & customer care') }}</h2>
        <a href="{{ route('after-sales.index', ['status' => 'open']) }}" class="btn btn-phoenix-secondary">{{ __('Open only') }}</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover crm-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">{{ __('Ticket') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Contact') }}</th>
                        <th>{{ __('Unit') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="pe-4">{{ __('Due') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td class="ps-4"><a href="{{ route('after-sales.show', $ticket) }}">#{{ $ticket->id }}</a></td>
                            <td>{{ $ticket->type->label() }}</td>
                            <td>{{ $ticket->contact?->name ?? '—' }}</td>
                            <td>{{ $ticket->inventoryUnit?->code ?? '—' }}</td>
                            <td><span class="badge badge-phoenix badge-phoenix-secondary">{{ $ticket->status->label() }}</span></td>
                            <td class="pe-4">{{ $ticket->scheduled_at?->format('M j, Y') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6"><div class="crm-empty">{{ __('No tickets yet.') }}</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())<div class="card-footer">{{ $tickets->links() }}</div>@endif
    </div>
</div>
@endsection
