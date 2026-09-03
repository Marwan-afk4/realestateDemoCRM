@extends('layouts.app')
@php $currentPage = 'developer-portal'; @endphp
@section('title', __('Developer inventory'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <h2 class="mb-4">{{ __('Remaining units') }} — {{ $developer->name_en ?? $developer->name_ar }}</h2>
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover crm-table mb-0">
                <thead><tr><th class="ps-4">{{ __('Code') }}</th><th>{{ __('Address') }}</th><th>{{ __('Status') }}</th><th>{{ __('Price') }}</th><th class="pe-4">{{ __('Buyer') }}</th></tr></thead>
                <tbody>
                    @foreach($units as $unit)
                        <tr>
                            <td class="ps-4"><a href="{{ route('inventory-units.show', $unit) }}">{{ $unit->code }}</a></td>
                            <td>{{ $unit->address() }}</td>
                            <td><span class="badge badge-phoenix badge-phoenix-secondary">{{ $unit->status->label() }}</span></td>
                            <td>{{ number_format($unit->price()) }}</td>
                            <td class="pe-4">{{ $unit->activeDeal?->contact?->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($units->hasPages())<div class="card-footer">{{ $units->links() }}</div>@endif
    </div>
</div>
@endsection
