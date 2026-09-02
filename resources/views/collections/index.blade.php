@extends('layouts.app')
@php $currentPage = 'collections'; @endphp
@section('title', __('Buyer collections'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Buyer collections') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Down payments and installments on sold units — not broker subscription payments.') }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('collections.index') }}" class="btn btn-sm {{ !request('filter') || request('filter') === 'overdue' ? 'btn-primary' : 'btn-phoenix-secondary' }}">{{ __('Overdue') }}</a>
            <a href="{{ route('collections.index', ['filter' => 'due']) }}" class="btn btn-sm {{ request('filter') === 'due' ? 'btn-primary' : 'btn-phoenix-secondary' }}">{{ __('Due soon') }}</a>
            <a href="{{ route('collections.index', ['filter' => 'all']) }}" class="btn btn-sm {{ request('filter') === 'all' ? 'btn-primary' : 'btn-phoenix-secondary' }}">{{ __('All') }}</a>
        </div>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table crm-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">{{ __('Due') }}</th>
                        <th>{{ __('Buyer') }}</th>
                        <th>{{ __('Unit') }}</th>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Remaining') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="pe-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($installments as $row)
                        <tr>
                            <td class="ps-4 text-nowrap {{ $row->status === \App\Enums\BuyerInstallmentStatus::Overdue ? 'text-danger fw-bold' : '' }}">{{ $row->due_date->format('Y-m-d') }}</td>
                            <td>
                                @if($row->plan?->deal?->contact)
                                    <a href="{{ route('contacts.show', $row->plan->deal->contact) }}">{{ $row->plan->deal->contact->name }}</a>
                                @else
                                    {{ $row->plan?->deal?->fullname ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $row->plan?->inventoryUnit?->code ?? '—' }}</td>
                            <td>{{ $row->label }}</td>
                            <td>{{ number_format($row->remaining()) }}</td>
                            <td><span class="badge badge-phoenix {{ $row->status->phoenixBadge() }}">{{ $row->status->label() }}</span></td>
                            <td class="pe-4">
                                @if($row->remaining() > 0 && $row->plan?->deal)
                                    <form method="POST" action="{{ route('collections.receipts.store', $row) }}" class="d-flex gap-1">
                                        @csrf
                                        <input type="number" step="0.01" name="amount" class="form-control form-control-sm" style="width:7rem" value="{{ $row->remaining() }}" required>
                                        <input type="text" name="reference" class="form-control form-control-sm" placeholder="{{ __('Ref') }}" style="width:6rem">
                                        <button class="btn btn-sm btn-primary">{{ __('Receipt') }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-5"><div class="crm-empty">{{ __('Nothing due.') }}</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($installments->hasPages())
            <div class="card-footer">{{ $installments->links() }}</div>
        @endif
    </div>
</div>
@endsection
