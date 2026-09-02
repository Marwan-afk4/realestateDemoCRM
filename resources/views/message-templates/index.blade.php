@extends('layouts.app')
@php $currentPage = 'message-templates'; @endphp
@section('title', __('Message Templates'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Message Templates') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('WhatsApp, SMS, and email copy used from the contact card.') }}</p>
        </div>
        <a href="{{ route('message-templates.create') }}" class="btn btn-primary">
            <span class="fas fa-plus me-2"></span>{{ __('Create') }}
        </a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover crm-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">{{ __('Name') }}</th>
                            <th>{{ __('Channel') }}</th>
                            <th>{{ __('Preview') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end pe-4">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $template)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $template->name }}</td>
                                <td><span class="badge {{ $template->channel->badgeClass() }}">{{ $template->channel->label() }}</span></td>
                                <td class="text-body-tertiary" style="max-width:22rem;">
                                    <span class="d-inline-block text-truncate" style="max-width:22rem;">{{ $template->body }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $template->is_active ? 'badge-phoenix badge-phoenix-success' : 'badge-phoenix badge-phoenix-secondary' }}">
                                        {{ $template->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('message-templates.edit', $template) }}" class="btn btn-sm btn-phoenix-secondary">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('message-templates.destroy', $template) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this template?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-phoenix-danger">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6"><div class="crm-empty">{{ __('No templates.') }}</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($templates->hasPages())
            <div class="card-footer">{{ $templates->links() }}</div>
        @endif
    </div>
</div>
@endsection
