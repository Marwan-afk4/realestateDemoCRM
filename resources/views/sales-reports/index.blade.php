@extends('layouts.app')
@php $currentPage = 'sales-reports'; @endphp
@section('title', __('Sales Reports'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="mb-2">{{ __('Sales Reports') }}</h2>
        <p class="text-body-tertiary mb-0">{{ __('How the desk is converting — not catalog size.') }}</p>
    </div>

    <div class="row g-3 mb-4">
        @php
            $stats = [
                ['label' => __('Tickets'), 'value' => $total, 'icon' => 'layers', 'class' => 'text-primary', 'bg' => 'bg-gradient-primary-soft'],
                ['label' => __('Won / Lost'), 'value' => $won.' / '.$lost, 'icon' => 'target', 'class' => 'text-success', 'bg' => 'bg-gradient-success-soft'],
                ['label' => __('Conversion'), 'value' => $conversion.'%', 'icon' => 'trending-up', 'class' => 'text-info', 'bg' => 'bg-gradient-info-soft'],
                ['label' => __('Avg days to close'), 'value' => $avgDays, 'icon' => 'clock', 'class' => 'text-warning', 'bg' => 'bg-gradient-warning-soft'],
                ['label' => __('Time to first contact (hours)'), 'value' => $firstContactHours ?? '—', 'icon' => 'phone', 'class' => 'text-primary', 'bg' => 'bg-gradient-primary-soft'],
                ['label' => __('Forecasted deal value'), 'value' => number_format($forecast, 0), 'icon' => 'bar-chart-2', 'class' => 'text-info', 'bg' => 'bg-gradient-info-soft'],
                ['label' => __('Closed commission'), 'value' => number_format($closedCommission, 0), 'icon' => 'dollar-sign', 'class' => 'text-success', 'bg' => 'bg-gradient-success-soft'],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div class="col-xl-3 col-md-6">
                <div class="card crm-stat {{ $stat['bg'] }}">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-body-tertiary mb-2">{{ $stat['label'] }}</h6>
                            <h3 class="{{ $stat['class'] }}">{{ $stat['value'] }}</h3>
                        </div>
                        <span class="crm-stat-icon {{ $stat['class'] }}">
                            <span data-feather="{{ $stat['icon'] }}" style="width:22px;height:22px;"></span>
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Leads by source') }}</h5>
                    @forelse ($bySource as $source => $count)
                        @php $max = max(1, $bySource->max()); $pct = round($count / $max * 100); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between fs-9 mb-1">
                                <span>{{ \App\Enums\ContactSource::tryFrom($source)?->label() ?? $source }}</span>
                                <strong>{{ $count }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar rounded-pill" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="crm-empty">{{ __('No data') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Conversion by stage') }}</h5>
                    @foreach ($stages as $stage)
                        @php $count = $byStage[$stage->value] ?? 0; $max = max(1, collect($byStage)->max()); $pct = round($count / $max * 100); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between fs-9 mb-1">
                                <span>{{ $stage->label() }}</span>
                                <strong>{{ $count }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar rounded-pill" style="width: {{ $pct }}%; background:#{{ $stage->color() }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body p-0">
                    <div class="p-3 pb-0"><h5>{{ __('Broker performance') }}</h5></div>
                    <div class="table-responsive">
                        <table class="table crm-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">{{ __('Broker') }}</th>
                                    <th class="text-center">{{ __('Assigned') }}</th>
                                    <th class="text-center">{{ __('Won') }}</th>
                                    <th class="text-center pe-3">{{ __('Lost') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($brokerStats as $row)
                                    <tr>
                                        <td class="ps-3 fw-semibold">{{ $row->brocker?->user?->full_name ?? $row->brocker_id }}</td>
                                        <td class="text-center">{{ $row->assigned }}</td>
                                        <td class="text-center text-success">{{ $row->won }}</td>
                                        <td class="text-center text-danger pe-3">{{ $row->lost }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-body-tertiary">{{ __('No data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Lost reasons') }}</h5>
                    @forelse ($lostReasons as $reason => $count)
                        @php $max = max(1, $lostReasons->max()); $pct = round($count / $max * 100); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between fs-9 mb-1">
                                <span>{{ \App\Enums\LostReason::tryFrom($reason)?->label() ?? $reason }}</span>
                                <strong>{{ $count }}</strong>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger rounded-pill" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="crm-empty">{{ __('No lost reasons yet') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
