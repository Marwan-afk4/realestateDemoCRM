@extends('layouts.app')

@php
    $currentPage = 'home';
@endphp

@section('title', 'الصفحه الرئيسية')

@section('content')
{{-- Header with Filter --}}
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="mb-3">{{ __('Dashboard Overview') }}</h2>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end align-items-center">
            <label class="me-2 mb-0">{{ __('Filter Period:') }}</label>
            <div class="position-relative">
                <select id="filterSelect" class="form-select form-select-sm" style="width: auto; min-width: 160px;">
                    <optgroup label="{{ __('Current Period') }}">
                        <option value="monthly" {{ $filter === 'monthly' ? 'selected' : '' }}>{{ __('This Month') }}</option>
                        <option value="yearly" {{ $filter === 'yearly' ? 'selected' : '' }}>{{ __('This Year') }}</option>
                        <option value="quarter" {{ $filter === 'quarter' ? 'selected' : '' }}>{{ __('This Quarter') }}</option>
                    </optgroup>
                    <optgroup label="{{ __('Previous Period') }}">
                        <option value="previous_month" {{ $filter === 'previous_month' ? 'selected' : '' }}>{{ __('Previous Month') }}</option>
                        <option value="previous_year" {{ $filter === 'previous_year' ? 'selected' : '' }}>{{ __('Previous Year') }}</option>
                        <option value="previous_quarter" {{ $filter === 'previous_quarter' ? 'selected' : '' }}>{{ __('Previous Quarter') }}</option>
                    </optgroup>
                    <optgroup label="{{ __('Last N Days') }}">
                        <option value="last_7_days" {{ $filter === 'last_7_days' ? 'selected' : '' }}>{{ __('Last 7 Days') }}</option>
                        <option value="last_30_days" {{ $filter === 'last_30_days' ? 'selected' : '' }}>{{ __('Last 30 Days') }}</option>
                        <option value="last_90_days" {{ $filter === 'last_90_days' ? 'selected' : '' }}>{{ __('Last 90 Days') }}</option>
                    </optgroup>
                </select>
                <div id="filterLoading" class="position-absolute top-50 end-0 translate-middle-y me-2" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">{{ __('Loading...') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Period Info --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-success-custom d-flex align-items-center">
            <span data-feather="calendar" class="me-2 text-success"></span>
            <span>
                <strong class="text-success">{{ __('Showing data for:') }}</strong>
                <span class="ms-2 adaptive-text">
                    @php
                        $startDate = \Carbon\Carbon::parse($statistics['period_start']);
                        $endDate = \Carbon\Carbon::parse($statistics['period_end']);
                    @endphp

                    @switch($filter)
                        @case('yearly')
                            {{ __('Year') }} {{ $startDate->format('Y') }}
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                            @break
                        @case('previous_year')
                            {{ __('Previous Year') }} {{ $startDate->format('Y') }}
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                            @break
                        @case('previous_month')
                            {{ __('Previous Month') }} - {{ $startDate->format('F Y') }}
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                            @break
                        @case('quarter')
                            {{ __('Current Quarter') }} (Q{{ $startDate->quarter }} {{ $startDate->format('Y') }})
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                            @break
                        @case('previous_quarter')
                            {{ __('Previous Quarter') }} (Q{{ $startDate->quarter }} {{ $startDate->format('Y') }})
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                            @break
                        @case('last_7_days')
                            {{ __('Last 7 Days') }}
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                            @break
                        @case('last_30_days')
                            {{ __('Last 30 Days') }}
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                            @break
                        @case('last_90_days')
                            {{ __('Last 90 Days') }}
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                            @break
                        @default
                            {{ __('Current Month') }} - {{ $startDate->format('F Y') }}
                            <small class="text-muted">({{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }})</small>
                    @endswitch
                </span>
            </span>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary-soft { background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%); }
    .bg-gradient-info-soft { background: linear-gradient(135deg, rgba(13, 202, 240, 0.1) 0%, rgba(13, 202, 240, 0.05) 100%); }
    .bg-gradient-success-soft { background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%); }
    .bg-gradient-warning-soft { background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%); }
</style>

{{-- Real Estate Submissions Section --}}
<h4 class="mb-3">{{ __('Real Estate Submissions') }}</h4>
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-gradient-primary-soft text-primary statistics-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">{{ __('Total Units Requests') }}</h6>
                        <h3 class="mb-0">{{ $sellRequestCount }}</h3>
                        <a href="{{ route('sell-requests.index') }}" class="btn btn-link p-0 mt-2 text-primary fs-9">
                            {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="text-primary opacity-50"><span data-feather="send" style="width:32px;height:32px;"></span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-gradient-info-soft text-info statistics-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">{{ __(' Mortgage Requests') }}</h6>
                        <h3 class="mb-0">{{ $installmentRequestCount }}</h3>
                        <a href="{{ route('apartment-installments.index') }}" class="btn btn-link p-0 mt-2 text-info fs-9">
                            {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="text-info opacity-50"><span data-feather="clock" style="width:32px;height:32px;"></span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-gradient-success-soft text-success statistics-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">{{ __('Total Users') }}</h6>
                        <h3 class="mb-0">{{ $userCount }}</h3>
                        <a href="{{ route('users.index') }}" class="btn btn-link p-0 mt-2 text-success fs-9">
                            {{ __('Manage Users') }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="text-success opacity-50"><span data-feather="users" style="width:32px;height:32px;"></span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-gradient-warning-soft text-warning statistics-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted">{{ __('Total Units') }}</h6>
                        <h3 class="mb-0">{{ $unitCount }}</h3>
                        <a href="{{ route('uptowns.index') }}" class="btn btn-link p-0 mt-2 text-warning fs-9">
                            {{ __('Manage Units') }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="text-warning opacity-50"><span data-feather="map-pin" style="width:32px;height:32px;"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row mb-4">

    {{-- Revenue Card --}}
    {{-- <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-primary text-white statistics-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">{{ __('Total Revenue') }}</h6>
                        <h3 class="mb-0">{{ number_format($statistics['total_revenue'], 2) }}</h3>
                        @if($comparison && isset($comparison['revenue_change']))
                            <div class="mt-1">
                                <small class="text-white-75 comparison-indicator">
                                    @if($comparison['revenue_change'] > 0)
                                        <i class="fas fa-arrow-up"></i> +{{ $comparison['revenue_change'] }}%
                                    @elseif($comparison['revenue_change'] < 0)
                                        <i class="fas fa-arrow-down"></i> {{ $comparison['revenue_change'] }}%
                                    @else
                                        <i class="fas fa-minus"></i> {{ __('No change') }}
                                    @endif
                                    {{ __('vs previous period') }}
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="text-white-50">
                        <span data-feather="dollar-sign" style="width:32px;height:32px;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Total Deals Card --}}
    {{-- <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-success text-white statistics-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">{{ __('Total Deals') }}</h6>
                        <h3 class="mb-0">{{ $statistics['total_deals'] }}</h3>
                        <small class="text-white-75">{{ $statistics['period_name'] }}</small>
                        @if($comparison && isset($comparison['deals_change']))
                            <div class="mt-1">
                                <small class="text-white-75 comparison-indicator">
                                    @if($comparison['deals_change'] > 0)
                                        <i class="fas fa-arrow-up"></i> +{{ $comparison['deals_change'] }}%
                                    @elseif($comparison['deals_change'] < 0)
                                        <i class="fas fa-arrow-down"></i> {{ $comparison['deals_change'] }}%
                                    @else
                                        <i class="fas fa-minus"></i> {{ __('No change') }}
                                    @endif
                                    {{ __('vs previous period') }}
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="text-white-50">
                        <span data-feather="trending-up" style="width:32px;height:32px;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Total Brokers Card --}}
    {{-- <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-info text-white statistics-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">{{ __('Total Brokers') }}</h6>
                        <h3 class="mb-0">{{ $statistics['total_brockers'] }}</h3>
                    </div>
                    <div class="text-white-50">
                        <span data-feather="users" style="width:32px;height:32px;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Pending Items Card --}}
    {{-- <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-warning text-white statistics-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">{{ __('Pending Items') }}</h6>
                        <h3 class="mb-0">{{ $statistics['open_complaints'] + $statistics['pending_training_requests'] + $statistics['pending_payments'] }}</h3>
                        <small class="text-white-75">{{ __('Needs Attention') }}</small>
                    </div>
                    <div class="text-white-50">
                        <span data-feather="alert-circle" style="width:32px;height:32px;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

{{-- Main Content Row --}}
<div class="row">
    {{-- Charts Section --}}
    <div class="col-xl-8">
        <div class="main-card mb-3 card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                {{-- Header Section --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h3 class="mb-1 text-dark fw-bold">
                            <i class="fa fa-chart-line text-primary me-2"></i>{{ __('Payouts Forecast by Execution Date') }}
                        </h3>
                        <p class="text-muted mb-0 small">{{ __('Sum of sell request prices grouped by their scheduled execution month') }}</p>
                    </div>
                    <div>
                        <select id="monthFilter" class="form-select form-select-sm border-0 shadow-sm bg-light fw-bold" style="width: auto; min-width: 160px; height: 38px;">
                            <option value="all">{{ __('All Months') }}</option>
                            <option value="1">{{ __('January') }}</option>
                            <option value="2">{{ __('February') }}</option>
                            <option value="3">{{ __('March') }}</option>
                            <option value="4">{{ __('April') }}</option>
                            <option value="5">{{ __('May') }}</option>
                            <option value="6">{{ __('June') }}</option>
                            <option value="7">{{ __('July') }}</option>
                            <option value="8">{{ __('August') }}</option>
                            <option value="9">{{ __('September') }}</option>
                            <option value="10">{{ __('October') }}</option>
                            <option value="11">{{ __('November') }}</option>
                            <option value="12">{{ __('December') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Interactive Payout KPIs --}}
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column justify-content-center">
                            <span class="text-muted d-block small mb-1">
                                <i class="fa fa-calendar-alt text-primary me-1"></i>{{ __('Current Year Total') }}
                            </span>
                            <h3 class="text-primary fw-bold mb-0">
                                {{ number_format($totalPayoutCurrentYear) }} <small class="fs-10 text-muted">EGP</small>
                            </h3>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column justify-content-center">
                            <span class="text-muted d-block small mb-1">
                                <i class="fa fa-wallet text-success me-1"></i>{{ __('All-Time Payouts Forecast') }}
                            </span>
                            <h3 class="text-success fw-bold mb-0">
                                {{ number_format($totalPayoutAllTime) }} <small class="fs-10 text-muted">EGP</small>
                            </h3>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column justify-content-center">
                            <span class="text-muted d-block small mb-1">
                                <i class="fa fa-hand-holding-usd text-warning me-1"></i>{{ __('Next Scheduled Payout') }}
                            </span>
                            @if($nextUpcomingPayout)
                                <h3 class="text-warning fw-bold mb-0 text-truncate" title="{{ number_format($nextUpcomingPayout->price) }} EGP">
                                    {{ number_format($nextUpcomingPayout->price) }} <small class="fs-10 text-muted">EGP</small>
                                </h3>
                                <small class="text-muted d-block" style="font-size: 0.72rem; margin-top: 2px;">
                                    {{ $nextUpcomingPayout->delivery_date_label }}
                                </small>
                            @else
                                <span class="text-muted fw-bold">{{ __('None Scheduled') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="p-3 border rounded-3 bg-light-subtle h-100 d-flex flex-column justify-content-center">
                            <span class="text-muted d-block small mb-1">
                                <i class="fa fa-clipboard-list text-info me-1"></i>{{ __('Total Scheduled Requests') }}
                            </span>
                            <h3 class="text-info fw-bold mb-0">
                                {{ \App\Models\SellRequest::whereNotNull('delivery_date')->count() }} <small class="fs-10 text-muted">{{ __('units') }}</small>
                            </h3>
                        </div>
                    </div>
                </div>

                {{-- Chart container --}}
                <div class="p-3 border rounded-3 bg-light-subtle">
                    <div class="echarts-execution-payouts" style="min-height:380px;width:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Sidebar --}}
    <div class="col-xl-4">
        {{-- Deal Status Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Deal Status') }}
                    <small class="text-muted">({{ $statistics['period_name'] }})</small>
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-success">{{ __('Approved') }}</span>
                        <span class="badge bg-success">{{ $statistics['deals_status']['approved'] }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $statistics['total_deals'] > 0 ? ($statistics['deals_status']['approved'] / $statistics['total_deals']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-warning">{{ __('Pending') }}</span>
                        <span class="badge bg-warning">{{ $statistics['deals_status']['pending'] }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ $statistics['total_deals'] > 0 ? ($statistics['deals_status']['pending'] / $statistics['total_deals']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-info">{{ __('Semi Done') }}</span>
                        <span class="badge bg-info">{{ $statistics['deals_status']['semidone'] }}</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $statistics['total_deals'] > 0 ? ($statistics['deals_status']['semidone'] / $statistics['total_deals']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-0">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-danger">{{ __('Rejected') }}</span>
                        <span class="badge bg-danger">{{ $statistics['deals_status']['rejected'] }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger" style="width: {{ $statistics['total_deals'] > 0 ? ($statistics['deals_status']['rejected'] / $statistics['total_deals']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Overview Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">{{ __('System Overview') }}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="text-info" data-feather="users"></i> {{ __('Total Developers') }}</span>
                        <span class="badge bg-info rounded-pill">{{ $statistics['total_developers'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="text-primary" data-feather="shield"></i> {{ __('Total Admins') }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $statistics['total_admins'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="text-danger" data-feather="alert-triangle"></i> {{ __('Open Complaints') }}</span>
                        <span class="badge bg-danger rounded-pill">{{ $statistics['open_complaints'] }}</span>
                    </li>
                    {{-- <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="text-success" data-feather="credit-card"></i> {{ __('Pending Payments') }}</span>
                        <span class="badge bg-success rounded-pill">{{ $statistics['pending_payments'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="text-warning" data-feather="book-open"></i> {{ __('Training Requests') }}</span>
                        <span class="badge bg-warning rounded-pill">{{ $statistics['pending_training_requests'] }}</span>
                    </li> --}}
                </ul>
            </div>
        </div>

        {{-- API Logs / Mobile Usage Card --}}
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Application Usage Ranking') }}</h5>
                <span class="badge bg-subtle-primary text-primary">{{ count($apiLogs) }} {{ __('Routes') }}</span>
            </div>
            <div class="card-body p-0">
                @if($apiLogs->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fa fa-chart-line mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mb-0 small">{{ __('No API traffic logged in this period.') }}</p>
                    </div>
                @else
                    @php
                        $totalVisits = $apiLogs->sum('visits');
                    @endphp
                    <div class="list-group list-group-flush" style="max-height: 380px; overflow-y: auto;">
                        @foreach($apiLogs as $index => $log)
                            @php
                                $percentage = $totalVisits > 0 ? ($log->visits / $totalVisits) * 100 : 0;
                                $badgeColor = match(strtoupper($log->method)) {
                                    'GET' => 'bg-subtle-success text-success',
                                    'POST' => 'bg-subtle-primary text-primary',
                                    'PUT', 'PATCH' => 'bg-subtle-warning text-warning',
                                    'DELETE' => 'bg-subtle-danger text-danger',
                                    default => 'bg-subtle-secondary text-secondary'
                                };
                                $rankBadge = match($index) {
                                    0 => 'bg-warning text-dark', // Gold
                                    1 => 'bg-secondary text-white', // Silver
                                    2 => 'bg-danger text-white', // Bronze
                                    default => 'bg-light text-muted'
                                };
                            @endphp
                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="badge rounded-circle {{ $rankBadge }} d-inline-flex align-items-center justify-content-center mt-1" style="width: 20px; height: 20px; font-size: 0.7rem;">
                                            {{ $index + 1 }}
                                        </span>
                                        <div class="d-flex flex-column" style="max-width: 180px;">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge {{ $badgeColor }} font-monospace px-2" style="font-size: 0.7rem; padding: 0.15rem 0.3rem;">
                                                    {{ strtoupper($log->method) }}
                                                </span>
                                                <span class="text-dark fw-bold text-truncate" style="font-size: 0.85rem;" title="{{ $log->title }}">
                                                    {{ $log->title }}
                                                </span>
                                            </div>
                                            {{-- <span class="text-muted small text-truncate" style="font-size: 0.72rem;" title="{{ $log->path }}">
                                                /{{ $log->path }}
                                            </span> --}}
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-black fw-bold">{{ $log->visits }} <small class="text-muted" style="font-size: 0.65rem;">{{ __('hits') }}</small></span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Top Plans Card --}}
        @if(count($mostPlans) > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Most Popular Plans') }}</h5>
            </div>
            <div class="card-body">
                @foreach($mostPlans as $index => $plan)
                <div class="d-flex justify-content-between align-items-center {{ $loop->last ? '' : 'mb-3' }}">
                    <div>
                        <h6 class="mb-1">{{ $plan['plan_name'] }}</h6>
                        <small class="text-muted">{{ $plan['count'] }} {{ __('subscriptions') }}</small>
                    </div>
                    <div class="text-end">
                        <div class="text-primary fw-bold">{{ number_format($plan['total_amount'], 2) }}</div>
                        <small class="text-muted">{{ number_format($plan['plan_price'], 2) }} {{ __('each') }}</small>
                    </div>
                </div>
                @if(!$loop->last)
                <hr>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.progress {
    background-color: rgba(0,0,0,0.1);
}

.progress-bar {
    transition: width 0.6s ease;
}

.badge {
    font-size: 0.75rem;
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}

.list-group-item:not(:last-child) {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.text-white-50 {
    color: rgba(255,255,255,0.5) !important;
}

.text-white-75 {
    color: rgba(255,255,255,0.75) !important;
}

/* Custom Alert Styles - Choose one by changing the class name */

/* Option 1: Modern Blue Gradient */
.alert-primary-custom {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(108, 117, 125, 0.05) 100%);
    border: 1px solid rgba(13, 110, 253, 0.2);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Option 2: Success Green */
.alert-success-custom {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%);
    border: 1px solid rgba(25, 135, 84, 0.2);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Option 3: Purple Gradient */
.alert-purple-custom {
    background: linear-gradient(135deg, rgba(102, 16, 242, 0.1) 0%, rgba(102, 16, 242, 0.05) 100%);
    border: 1px solid rgba(102, 16, 242, 0.2);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Option 4: Dark Theme */
.alert-dark-custom {
    background: linear-gradient(135deg, rgba(33, 37, 41, 0.9) 0%, rgba(52, 58, 64, 0.8) 100%);
    border: 1px solid rgba(108, 117, 125, 0.3);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #fff;
}

/* Option 5: Orange/Warning */
.alert-warning-custom {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%);
    border: 1px solid rgba(255, 193, 7, 0.2);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

/* Option 6: Teal/Cyan */
.alert-teal-custom {
    background: linear-gradient(135deg, rgba(32, 201, 151, 0.1) 0%, rgba(32, 201, 151, 0.05) 100%);
    border: 1px solid rgba(32, 201, 151, 0.2);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.alert-info {
    background-color: rgba(13, 110, 253, 0.1);
    border-color: rgba(13, 110, 253, 0.2);
    color: #0d6efd;
}

.bg-gradient-primary-soft { background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%); border: 1px solid rgba(13, 110, 253, 0.2); }
.bg-gradient-info-soft { background: linear-gradient(135deg, rgba(13, 202, 240, 0.1) 0%, rgba(13, 202, 240, 0.05) 100%); border: 1px solid rgba(13, 202, 240, 0.2); }
.bg-gradient-success-soft { background: linear-gradient(135deg, rgba(25, 135, 84, 0.1) 0%, rgba(25, 135, 84, 0.05) 100%); border: 1px solid rgba(25, 135, 84, 0.2); }
.bg-gradient-warning-soft { background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.05) 100%); border: 1px solid rgba(255, 193, 7, 0.2); }


.statistics-card {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#filterSelect:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.comparison-indicator {
    font-size: 0.75rem;
    opacity: 0.9;
}

.comparison-indicator .fas {
    font-size: 0.7rem;
}

optgroup {
    font-weight: bold;
    color: #6c757d;
}

optgroup option {
    font-weight: normal;
    color: #212529;
}
</style>
@endpush

@push('scripts')
<!-- Feather Icons (no defer) -->
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<!-- ECharts (no defer) -->
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

@php
    $safeUserCounts = $userMonthlyCounts ?? [12, 15, 20, 18, 22, 30, 25, 28, 24, 26, 30, 33];
    $safeBrockerCounts = $brockerMonthlyCounts ?? [5, 7, 9, 6, 10, 12, 8, 11, 9, 13, 14, 16];
@endphp

<script>
        function renderPayoutChart(selector, seriesName, labels, seriesData, isDaily = false, selectedMonthName = '') {
            const el = document.querySelector(selector);
            if (!el) return;

            if (echarts.getInstanceByDom(el)) {
                echarts.getInstanceByDom(el).dispose();
            }

            const isDark = localStorage.getItem('phoenixTheme') === 'dark';

            const chart = echarts.init(el, null, {
                backgroundColor: 'transparent'
            });

            chart.setOption({
                tooltip: {
                    trigger: 'axis',
                    backgroundColor: isDark ? '#333' : '#fff',
                    textStyle: {
                        color: isDark ? '#fff' : '#000'
                    },
                    borderWidth: 0,
                    formatter: function(params) {
                        const val = Number(params[0].value).toLocaleString();
                        const labelPrefix = isDaily ? `${selectedMonthName} ` : '';
                        return `
                        <div class="p-1">
                            <small class="text-muted d-block mb-1">${labelPrefix}${params[0].name}</small>
                            <h6 class="fs-9 mb-0" style="color:${isDark ? '#fff' : '#333'}">
                                <span class="fas fa-circle me-1" style='color:${params[0].color}'></span>
                                ${params[0].seriesName} : <strong>${val} EGP</strong>
                            </h6>
                        </div>
                    `;
                    }
                },
                xAxis: {
                    type: 'category',
                    data: labels,
                    boundaryGap: false,
                    axisLabel: {
                        color: isDark ? '#ddd' : '#333',
                        formatter: value => isDaily ? value : value.substring(0, 3)
                    },
                    axisLine: {
                        lineStyle: {
                            color: isDark ? '#555' : '#ccc'
                        }
                    }
                },
                yAxis: {
                    type: 'value',
                    axisLabel: {
                        color: isDark ? '#ddd' : '#333',
                        formatter: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000) + 'M';
                            } else if (value >= 1000) {
                                return (value / 1000) + 'k';
                            }
                            return value;
                        }
                    },
                    splitLine: {
                        lineStyle: {
                            color: isDark ? '#444' : '#eee',
                            type: 'dashed'
                        }
                    }
                },
                series: [{
                    name: seriesName,
                    type: 'line',
                    data: seriesData,
                    smooth: true,
                    symbol: 'circle',
                    symbolSize: 8,
                    showSymbol: false,
                    lineStyle: {
                        width: 3,
                        color: '#2563eb'
                    },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(37, 99, 235, 0.25)' },
                            { offset: 1, color: 'rgba(37, 99, 235, 0.01)' }
                        ])
                    },
                    itemStyle: {
                        color: '#2563eb',
                        borderWidth: 2,
                        borderColor: '#fff'
                    }
                }]
            });
        }

        function initHomePageScripts() {
            // Initialize feather icons
            feather.replace();

            const sellRequests = @json($sellRequestsData);
            const currentYear = {{ now()->year }};
            const monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            function updateChart() {
                const monthFilter = document.getElementById('monthFilter');
                if (!monthFilter) return;
                const filterVal = monthFilter.value;

                if (filterVal === 'all') {
                    // Calculate monthly totals
                    const monthlyTotals = Array(12).fill(0);
                    sellRequests.forEach(req => {
                        if (!req.delivery_date) return;
                        const parts = req.delivery_date.split('-');
                        const reqYear = parseInt(parts[0]);
                        const reqMonth = parseInt(parts[1]); // 1-12
                        if (reqYear === currentYear) {
                            monthlyTotals[reqMonth - 1] += parseFloat(req.price || 0);
                        }
                    });
                    renderPayoutChart('.echarts-execution-payouts', 'Delivery Payouts', monthNames, monthlyTotals, false);
                } else {
                    const monthIdx = parseInt(filterVal); // 1-12
                    // Delivery is month/year only — show that month's total as a single bar
                    let monthTotal = 0;
                    sellRequests.forEach(req => {
                        if (!req.delivery_date) return;
                        const parts = req.delivery_date.split('-');
                        const reqYear = parseInt(parts[0]);
                        const reqMonth = parseInt(parts[1]); // 1-12

                        if (reqYear === currentYear && reqMonth === monthIdx) {
                            monthTotal += parseFloat(req.price || 0);
                        }
                    });

                    const selectedMonthName = monthNames[monthIdx - 1];
                    renderPayoutChart('.echarts-execution-payouts', 'Delivery Payouts', [selectedMonthName], [monthTotal], true, selectedMonthName);
                }
            }

            // Setup filter listener
            const monthFilterEl = document.getElementById('monthFilter');
            if (monthFilterEl) {
                const newMonthFilterEl = monthFilterEl.cloneNode(true);
                monthFilterEl.parentNode.replaceChild(newMonthFilterEl, monthFilterEl);
                newMonthFilterEl.addEventListener('change', updateChart);
            }

            // Initial render
            updateChart();

            // Re-render feather icons after DOM updates
            setTimeout(() => {
                feather.replace();
            }, 100);

            // Handle filter change for global period filter
            const filterSelect = document.getElementById('filterSelect');
            const filterLoading = document.getElementById('filterLoading');

            if (filterSelect) {
                filterSelect.addEventListener('change', function() {
                    if (filterLoading) {
                        filterLoading.style.display = 'block';
                    }
                    this.disabled = true;
                    const currentUrl = new URL(window.location);
                    currentUrl.searchParams.set('filter', this.value);
                    window.location.href = currentUrl.toString();
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHomePageScripts);
        } else {
            initHomePageScripts();
        }

        document.addEventListener('turbo:load', initHomePageScripts);
        document.addEventListener('livewire:load', initHomePageScripts);

        // ✅ إعادة رسم الرسوم عند تغيير الثيم (Light/Dark)
        const observer = new MutationObserver(() => {
            initHomePageScripts();
        });

        observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });

        // ✅ دعم الـ event لو الثيم بيبعت إشارة
        // ✅ لما localStorage يتغير (مثلاً phoenixTheme يتبدل) على نفس الصفحة أو من صفحة تانية
        window.addEventListener('storage', function(e) {
            if (e.key === 'phoenixTheme') {
                initHomePageScripts();
            }
        });

        // ✅ لو عندك زرار بيغير الثيم في نفس الصفحة
        // اعمل بعد ما تغير localStorage.dispatchEvent(new Event('themeChanged'));
        window.addEventListener('themeChanged', () => {
            initHomePageScripts();
        });
    </script>
@endpush

