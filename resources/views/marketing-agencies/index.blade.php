@extends('layouts.app')
@php $currentPage = 'marketing-agencies'; @endphp
@section('title', __('Marketing agencies'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ __('Marketing agencies') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Agency companies with agent logins and lead budgets.') }}</p>
        </div>
        <a href="{{ route('marketing-agencies.create') }}" class="btn btn-primary">{{ __('New agency') }}</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover crm-table mb-0">
                <thead><tr><th class="ps-4">{{ __('Agency') }}</th><th>{{ __('Agents') }}</th><th>{{ __('Leads') }}</th><th class="pe-4"></th></tr></thead>
                <tbody>
                    @forelse ($agencies as $agency)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('marketing-agencies.show', $agency) }}">{{ $agency->name }}</a>
                                @if($agency->email)<div class="fs-9 text-body-tertiary">{{ $agency->email }}</div>@endif
                            </td>
                            <td>{{ $agency->agents_count }}</td>
                            <td>{{ $agency->leads_count }}</td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('agency.workspace', ['agency_id' => $agency->id]) }}" class="btn btn-sm btn-phoenix-secondary">{{ __('Workspace') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6"><div class="crm-empty">{{ __('No agencies yet.') }}</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($agencies->hasPages())<div class="card-footer">{{ $agencies->links() }}</div>@endif
    </div>
</div>
@endsection
