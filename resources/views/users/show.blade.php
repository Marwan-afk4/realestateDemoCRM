@extends('layouts.app')
@php
	$currentPage = 'users';
@endphp
@section('title', $user->full_name)
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('Users') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $user->full_name }}</li>
                </ol>
            </nav>
            <h1 class="mb-0 fw-bold text-black">{{ __('User Profile') }}: {{ $user->full_name }}</h1>
        </div>
        <div>
            <a href="{{ route('users.index') }}" class="btn btn-phoenix-secondary btn-sm me-1">
                <i class="fa fa-arrow-left me-1"></i> {{__('Back')}}
            </a>
            <a href='{{ route('users.edit', $user) }}' class="btn btn-warning btn-sm">
                <i class="fa fa-edit me-1"></i> {{ __('Edit User') }}
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: User details card -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card border-0 shadow-sm overflow-hidden position-relative h-100">
                <!-- Profile Header Banner -->
                <div class="bg-gradient-primary-to-secondary p-4" style="height: 100px; background: linear-gradient(135deg, #3874ff 0%, #1e40af 100%);"></div>
                <div class="card-body pt-0 text-center position-relative">
                    <!-- Avatar overlapping banner -->
                    <div class="avatar avatar-5xl mb-3" style="margin-top: -50px;">
                        <img src="/phoenix/assets/img/team/avatar.webp" class="rounded-circle border border-3 border-white shadow mx-auto" alt="{{ $user->full_name }}" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <h3 class="mb-1 text-black fw-bold">{{ $user->full_name }}</h3>
                    <p class="text-muted mb-2"><i class="fa fa-user-tag me-1 text-primary"></i> {{ ucfirst($user->role) }}</p>
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        {!! $user->status->badge() !!}
                    </div>

                    <!-- Stats Row -->
                    <div class="row g-2 border-top border-bottom py-3 mb-4">
                        <div class="col-6 border-end">
                            <h5 class="mb-0 fw-bold text-primary">{{ $user->sellRequests->count() }}</h5>
                            <small class="text-muted small">{{ __('Sell Requests') }}</small>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-0 fw-bold text-primary">{{ $user->buyAppartmentInstallments->count() }}</h5>
                            <small class="text-muted small">{{ __('Installment Requests') }}</small>
                        </div>
                    </div>

                    <div class="alert alert-subtle-primary py-2 mb-4 d-flex justify-content-between align-items-center">
                        <span class="small fw-bold text-primary"><i class="fa fa-building me-1"></i> {{ __('Total Requested Units') }}</span>
                        <span class="badge bg-primary fs-9 rounded-pill">{{ $user->sellRequests->count() + $user->buyAppartmentInstallments->count() }}</span>
                    </div>

                    <!-- Profile Details List -->
                    <div class="text-start">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-l me-3 bg-light rounded text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa fa-id-card"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.2;">{{ __('User ID') }}</small>
                                <span class="fw-bold text-black" style="font-size: 0.9rem;">{{ $user->id }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-l me-3 bg-light rounded text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="text-truncate" style="max-width: 80%;">
                                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.2;">{{ __('Email Address') }}</small>
                                <a href="mailto:{{ $user->email }}" class="fw-bold text-black text-decoration-none" style="font-size: 0.9rem;">{{ $user->email }}</a>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-l me-3 bg-light rounded text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.2;">{{ __('Phone Number') }}</small>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-black me-2" style="font-size: 0.9rem;">{{ $user->phone }}</span>
                                    <a href="tel:{{ $user->phone }}" class="btn btn-phoenix-success btn-xs px-2 py-0"><i class="fa fa-phone fs-9"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-l me-3 bg-light rounded text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa fa-calendar-day"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.2;">{{ __('Age') }}</small>
                                <span class="fw-bold text-black" style="font-size: 0.9rem;">{{ $user->age ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-l me-3 bg-light rounded text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.2;">{{ __('Governance') }}</small>
                                <span class="fw-bold text-black" style="font-size: 0.9rem;">{{ $user->governce ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-1">
                            <div class="avatar avatar-l me-3 bg-light rounded text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.2;">{{ __('Member Since') }}</small>
                                <span class="fw-bold text-black" style="font-size: 0.9rem;">
                                    {{ $user->created_at?->format('M d, Y') ?? '-' }} 
                                    <small class="text-muted">({{ $user->created_at?->diffForHumans() }})</small>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Tabs with lists of requests -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white pb-0 border-0">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs nav-tabs-phoenix mb-0" id="userRequestsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sell-requests-tab" data-bs-toggle="tab" data-bs-target="#sell-requests-pane" type="button" role="tab" aria-controls="sell-requests-pane" aria-selected="true">
                                <i class="fa fa-home me-2"></i>{{ __('Sell Requests') }} 
                                <span class="badge bg-primary rounded-pill ms-1">{{ $user->sellRequests->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="installment-requests-tab" data-bs-toggle="tab" data-bs-target="#installment-requests-pane" type="button" role="tab" aria-controls="installment-requests-pane" aria-selected="false">
                                <i class="fa fa-file-invoice-dollar me-2"></i>{{ __('Installment Requests') }} 
                                <span class="badge bg-primary rounded-pill ms-1">{{ $user->buyAppartmentInstallments->count() }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <!-- Tabs Content -->
                    <div class="tab-content" id="userRequestsTabContent">
                        <!-- Sell Requests Tab Pane -->
                        <div class="tab-pane fade show active" id="sell-requests-pane" role="tabpanel" aria-labelledby="sell-requests-tab">
                            @if($user->sellRequests->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fa fa-home text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mb-0 fw-bold">{{ __('No sell requests found for this user.') }}</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Type & Subtype') }}</th>
                                                <th>{{ __('Location') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Visibility') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->sellRequests as $sellRequest)
                                                <tr>
                                                    <td><strong>#{{ $sellRequest->id }}</strong></td>
                                                    <td>
                                                        <span class="d-block fw-bold text-black">{{ $sellRequest->uptownType?->name_en ?? ($sellRequest->uptownType?->name ?? '-') }}</span>
                                                        <small class="text-muted">{{ $sellRequest->unitSubType?->name_en ?? '-' }}</small>
                                                    </td>
                                                    <td>{{ $sellRequest->city }} / {{ $sellRequest->area }}</td>
                                                    <td>
                                                        <span class="text-primary fw-bold">{{ number_format($sellRequest->price) }}</span> 
                                                        <small class="text-muted font-size-xs">EGP</small>
                                                    </td>
                                                    <td>
                                                        @if($sellRequest->visibility === 'public')
                                                            <span class="badge badge-phoenix badge-phoenix-primary"><i class="fa fa-globe me-1"></i>{{ __('Public') }}</span>
                                                        @else
                                                            <span class="badge badge-phoenix badge-phoenix-secondary"><i class="fa fa-eye-slash me-1"></i>{{ __('Private') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($sellRequest->status === 'contacted')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ __('Contacted') }}</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">{{ __('Pending') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex justify-content-end gap-1">
                                                            <button class="btn btn-phoenix-primary btn-icon btn-sm" data-bs-toggle="modal" data-bs-target="#sellRequestModal{{ $sellRequest->id }}" title="{{ __('Quick View Details') }}">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                            @if($sellRequest->status !== 'contacted')
                                                                <form action="{{ route('sell-requests.update-status', $sellRequest) }}" method="POST" style="display:inline">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="contacted">
                                                                    <button type="submit" class="btn btn-phoenix-success btn-icon btn-sm" title="{{ __('Mark Contacted') }}">
                                                                        <i class="fa fa-check"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <a href="{{ route('sell-requests.show', $sellRequest) }}" class="btn btn-phoenix-info btn-icon btn-sm" title="{{ __('Open Request Page') }}">
                                                                <i class="fa fa-external-link-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Installment Requests Tab Pane -->
                        <div class="tab-pane fade" id="installment-requests-pane" role="tabpanel" aria-labelledby="installment-requests-tab">
                            @if($user->buyAppartmentInstallments->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fa fa-file-invoice-dollar text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mb-0 fw-bold">{{ __('No installment requests found for this user.') }}</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Requested Apartment') }}</th>
                                                <th>{{ __('Apartment Price') }}</th>
                                                <th>{{ __('Job Title') }}</th>
                                                <th>{{ __('Monthly Income') }}</th>
                                                <th>{{ __('Desired Installment') }}</th>
                                                <th>{{ __('Term') }}</th>
                                                <th>{{ __('Deposit') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->buyAppartmentInstallments as $buyAppartmentInstallment)
                                                <tr>
                                                    <td><strong>#{{ $buyAppartmentInstallment->id }}</strong></td>
                                                    <td>
                                                        @if($buyAppartmentInstallment->apartment)
                                                            <span class="d-block fw-bold text-black text-truncate" style="max-width: 180px;">{{ $buyAppartmentInstallment->apartment->description }}</span>
                                                            <small class="text-muted"><i class="fa fa-map-marker-alt me-1"></i>{{ $buyAppartmentInstallment->city }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($buyAppartmentInstallment->apartment && $buyAppartmentInstallment->apartment->strat_price)
                                                            <span class="text-primary fw-bold">{{ number_format($buyAppartmentInstallment->apartment->strat_price) }}</span> 
                                                            <small class="text-muted font-size-xs">EGP</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $buyAppartmentInstallment->job_title }}</td>
                                                    <td>
                                                        <span class="text-primary fw-bold">{{ number_format($buyAppartmentInstallment->monthly_income) }}</span> 
                                                        <small class="text-muted font-size-xs">EGP</small>
                                                    </td>
                                                    <td>
                                                        @if($buyAppartmentInstallment->monthly_installment)
                                                            <span class="text-success fw-bold">{{ number_format($buyAppartmentInstallment->monthly_installment) }}</span> 
                                                            <small class="text-muted font-size-xs">EGP</small>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td><span class="fw-bold text-black">{{ $buyAppartmentInstallment->years_of_installment }}</span> {{ __('Yrs') }}</td>
                                                    <td><span class="fw-bold text-black">{{ $buyAppartmentInstallment->deposit_percetage }}%</span></td>
                                                    <td>
                                                        @if($buyAppartmentInstallment->status === 'contacted')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">{{ __('Contacted') }}</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">{{ __('Pending') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="d-flex justify-content-end gap-1">
                                                            <button class="btn btn-phoenix-primary btn-icon btn-sm" data-bs-toggle="modal" data-bs-target="#installmentModal{{ $buyAppartmentInstallment->id }}" title="{{ __('Quick View Details') }}">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                            @if($buyAppartmentInstallment->status !== 'contacted')
                                                                <form action="{{ route('apartment-installments.update-status', $buyAppartmentInstallment) }}" method="POST" style="display:inline">
                                                                    @csrf
                                                                    <input type="hidden" name="status" value="contacted">
                                                                    <button type="submit" class="btn btn-phoenix-success btn-icon btn-sm" title="{{ __('Mark Contacted') }}">
                                                                        <i class="fa fa-check"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <a href="{{ route('apartment-installments.show', $buyAppartmentInstallment) }}" class="btn btn-phoenix-info btn-icon btn-sm" title="{{ __('Open Request Page') }}">
                                                                <i class="fa fa-external-link-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== MODALS ========================================== -->

<!-- Sell Request Modals -->
@foreach($user->sellRequests as $sellRequest)
    <div class="modal fade" id="sellRequestModal{{ $sellRequest->id }}" tabindex="-1" aria-labelledby="sellRequestModalLabel{{ $sellRequest->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-black" id="sellRequestModalLabel{{ $sellRequest->id }}">
                        <i class="fa fa-home text-primary me-2"></i>{{ __('Sell Request Details') }} #{{ $sellRequest->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Status Header block -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 p-3 bg-light rounded border border-light-subtle">
                        <div>
                            <span class="text-muted d-block small">{{ __('Created At') }}</span>
                            <span class="fw-bold text-black">{{ $sellRequest->created_at?->format('M d, Y H:i') }} <small class="text-muted">({{ $sellRequest->created_at?->diffForHumans() }})</small></span>
                        </div>
                        <div class="d-flex gap-2">
                            @if($sellRequest->status === 'contacted')
                                <span class="badge bg-success px-3 py-2 fs-9">{{ __('Contacted') }}</span>
                            @else
                                <span class="badge bg-warning px-3 py-2 fs-9">{{ __('Pending') }}</span>
                            @endif
                            
                            @if($sellRequest->visibility === 'public')
                                <span class="badge bg-primary px-3 py-2 fs-9"><i class="fa fa-globe me-1"></i> {{ __('Public') }}</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 fs-9"><i class="fa fa-eye-slash me-1"></i> {{ __('Private') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left Pane: Property details -->
                        <div class="col-md-7 border-end">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa fa-info-circle me-1"></i> {{ __('Property Information') }}</h6>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('Unit Type') }}</small>
                                    <span class="fw-bold text-black">{{ $sellRequest->uptownType?->name_en ?? ($sellRequest->uptownType?->name ?? '-') }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('Unit Sub Type') }}</small>
                                    <span class="fw-bold text-black">{{ $sellRequest->unitSubType?->name_en ?? '-' }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block small">{{ __('Price') }}</small>
                                    <h3 class="text-primary fw-bold mb-0">{{ number_format($sellRequest->price) }} <small class="text-muted font-size-xs">EGP</small></h3>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block small">{{ __('Location') }}</small>
                                    <span class="fw-bold text-black">
                                        <i class="fa fa-map-marker-alt text-danger me-1"></i>
                                        {{ $sellRequest->country }}, {{ $sellRequest->city }}, {{ $sellRequest->area }}
                                    </span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block small">{{ __('Execution Date') }}</small>
                                    <h3 class="text-success fw-bold mb-0">
                                        <i class="fa fa-calendar-alt text-success me-1"></i>
                                        {{ $sellRequest->execution_date?->label() ?? '-' }}
                                    </h3>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block small">{{ __('Delivery Date') }}</small>
                                    <h3 class="text-primary fw-bold mb-0">
                                        <i class="fa fa-truck text-primary me-1"></i>
                                        {{ $sellRequest->delivery_date_label ?? '-' }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Amenity Icons Grid -->
                            <h6 class="fw-bold text-primary mb-3"><i class="fa fa-sliders-h me-1"></i> {{ __('Specifications') }}</h6>
                            <div class="row g-2 mb-4">
                                <div class="col-3 text-center">
                                    <div class="p-2 border rounded bg-light">
                                        <i class="fa fa-door-open text-primary mb-1 d-block"></i>
                                        <span class="d-block fw-bold text-black">{{ $sellRequest->rooms_no ?? '-' }}</span>
                                        <small class="text-muted font-size-xs" style="font-size: 0.65rem;">{{ __('Rooms') }}</small>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="p-2 border rounded bg-light">
                                        <i class="fa fa-bath text-primary mb-1 d-block"></i>
                                        <span class="d-block fw-bold text-black">{{ $sellRequest->bathrooms_no ?? '-' }}</span>
                                        <small class="text-muted font-size-xs" style="font-size: 0.65rem;">{{ __('Baths') }}</small>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="p-2 border rounded bg-light">
                                        <i class="fa fa-vector-square text-primary mb-1 d-block"></i>
                                        <span class="d-block fw-bold text-black">{{ $sellRequest->space ?? '-' }} m²</span>
                                        <small class="text-muted font-size-xs" style="font-size: 0.65rem;">{{ __('Space') }}</small>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="p-2 border rounded bg-light">
                                        <i class="fa fa-building text-primary mb-1 d-block"></i>
                                        <span class="d-block fw-bold text-black">{{ $sellRequest->floor_no ?? '-' }}</span>
                                        <small class="text-muted font-size-xs" style="font-size: 0.65rem;">{{ __('Floor') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('Developer') }}</small>
                                    <span class="fw-bold text-black">{{ $sellRequest->developer?->name ?? '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('Compound') }}</small>
                                    <span class="fw-bold text-black">{{ $sellRequest->compound?->compound_name ?? '-' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('Garden Area') }}</small>
                                    <span class="fw-bold text-black">
                                        {{ $sellRequest->garden_area ? __('Yes') : __('No') }}
                                        @if($sellRequest->garden_space)
                                            ({{ $sellRequest->garden_space }} m²)
                                        @endif
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('Finishing') }}</small>
                                    <span class="fw-bold text-black">
                                        @switch($sellRequest->finishing)
                                            @case('finished')
                                                {{ __('Finished') }}
                                                @break
                                            @case('semi_finished')
                                                {{ __('Semi Finished') }}
                                                @break
                                            @case('unfinished')
                                                {{ __('Unfinished') }}
                                                @break
                                            @default
                                                -
                                        @endswitch
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('Delivery Date') }}</small>
                                    <span class="fw-bold text-black">{{ $sellRequest->delivery_date_label ?? '-' }}</span>
                                </div>
                            </div>

                            <!-- Installment Details -->
                            @if($sellRequest->installments)
                                <div class="p-3 border border-success rounded bg-light mb-4">
                                    <h6 class="text-success fw-bold mb-3"><i class="fa fa-file-invoice-dollar me-1"></i> {{ __('Installment Details') }}</h6>
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <small class="text-muted d-block font-size-xs">{{ __('Total Price') }}</small>
                                            <span class="fw-bold text-black small">{{ number_format($sellRequest->installments_total_price) }}</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block font-size-xs">{{ __('Years Left') }}</small>
                                            <span class="fw-bold text-black small">{{ $sellRequest->installments_years_left }} / {{ $sellRequest->installments_years }}</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block font-size-xs">{{ __('Price / Year') }}</small>
                                            <span class="fw-bold text-black small">{{ number_format($sellRequest->installments_price_per_year) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($sellRequest->notes)
                                <div class="mb-4">
                                    <small class="text-muted d-block fw-bold mb-1">{{ __('Notes') }}</small>
                                    <div class="p-3 border rounded bg-light font-italic small" style="font-size: 0.85rem;">
                                        "{{ $sellRequest->notes }}"
                                    </div>
                                </div>
                            @endif

                            <!-- Extra Metadata -->
                            @if(!empty($sellRequest->extra_data))
                                <h6 class="fw-bold text-primary mb-3"><i class="fa fa-cubes me-1"></i> {{ __('Additional Metadata') }}</h6>
                                <div class="row g-3 mb-4">
                                    @foreach($sellRequest->extra_data as $key => $value)
                                        <div class="col-6">
                                            <small class="text-muted d-block small">{{ ucfirst(str_replace('_', ' ', $key)) }}</small>
                                            <span class="fw-bold text-black small">{{ is_array($value) ? implode(', ', $value) : ($value === true ? 'Yes' : ($value === false ? 'No' : $value)) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Images Gallery -->
                            @if($sellRequest->images->count() > 0)
                                <h6 class="fw-bold text-primary mb-3"><i class="fa fa-images me-1"></i> {{ __('Unit Images') }}</h6>
                                <div class="row g-2">
                                    @foreach($sellRequest->images as $img)
                                        <div class="col-4 text-center">
                                            <a href="{{ asset('storage/' . $img->image) }}" target="_blank" class="d-block rounded overflow-hidden border shadow-sm hover-zoom">
                                                <img src="{{ asset('storage/' . $img->image) }}" class="img-fluid w-100" style="height: 70px; object-fit: cover;" alt="{{ $img->key }}">
                                            </a>
                                            <small class="text-muted d-block mt-1 font-size-xs" style="font-size: 0.65rem;">{{ ucfirst(str_replace('_', ' ', $img->key)) }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Right Pane: Docs & Actions -->
                        <div class="col-md-5 ps-md-4 mt-4 mt-md-0">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa fa-file-contract me-1"></i> {{ __('Identity Documents') }}</h6>
                            
                            <div class="row g-2 mb-4">
                                <div class="col-6 text-center">
                                    <small class="text-muted d-block mb-1 small">{{ __('Front Image') }}</small>
                                    @if($sellRequest->identity_front_image)
                                        <a href="{{ $sellRequest->identity_front_image }}" target="_blank" class="d-block border rounded p-1 shadow-sm bg-white hover-zoom">
                                            <img src="{{ $sellRequest->identity_front_image }}" class="img-fluid rounded" alt="Front" style="max-height: 100px; object-fit: contain; width: 100%;">
                                        </a>
                                    @else
                                        <div class="p-3 border rounded bg-light text-muted small">{{ __('No Image') }}</div>
                                    @endif
                                </div>
                                <div class="col-6 text-center">
                                    <small class="text-muted d-block mb-1 small">{{ __('Back Image') }}</small>
                                    @if($sellRequest->identity_back_image)
                                        <a href="{{ $sellRequest->identity_back_image }}" target="_blank" class="d-block border rounded p-1 shadow-sm bg-white hover-zoom">
                                            <img src="{{ $sellRequest->identity_back_image }}" class="img-fluid rounded" alt="Back" style="max-height: 100px; object-fit: contain; width: 100%;">
                                        </a>
                                    @else
                                        <div class="p-3 border rounded bg-light text-muted small">{{ __('No Image') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <small class="text-muted d-block mb-2 fw-bold small">{{ __('Detailed PDF Document') }}</small>
                                @if($sellRequest->detailed_pdf)
                                    <a href="{{ $sellRequest->detailed_pdf }}" target="_blank" class="btn btn-phoenix-primary w-100 py-2">
                                        <i class="fa fa-file-pdf me-2 text-danger"></i> {{ __('View Detailed PDF') }}
                                    </a>
                                @else
                                    <div class="alert alert-warning py-2 small mb-0"><i class="fa fa-info-circle me-1"></i> {{ __('No PDF attached') }}</div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <small class="text-muted d-block mb-2 fw-bold small">{{ __('Video') }}</small>
                                @if($sellRequest->video)
                                    <video controls class="w-100 rounded border" style="max-height: 220px; background: #000;">
                                        <source src="{{ $sellRequest->video }}">
                                    </video>
                                    <a href="{{ $sellRequest->video }}" target="_blank" class="btn btn-phoenix-secondary btn-sm w-100 mt-2">
                                        <i class="fa fa-external-link-alt me-1"></i> {{ __('Open Video') }}
                                    </a>
                                @else
                                    <div class="alert alert-light border py-2 small mb-0 text-muted"><i class="fa fa-info-circle me-1"></i> {{ __('No video attached') }}</div>
                                @endif
                            </div>

                            @if($sellRequest->unit_plan)
                                <div class="mb-4">
                                    <small class="text-muted d-block mb-2 fw-bold small">{{ __('Unit Plan Image') }}</small>
                                    <a href="{{ $sellRequest->unit_plan }}" target="_blank" class="d-block border rounded p-1 shadow-sm bg-white hover-zoom text-center">
                                        <img src="{{ $sellRequest->unit_plan }}" class="img-fluid rounded" alt="Unit Plan" style="max-height: 120px; object-fit: contain; width: 100%;">
                                    </a>
                                </div>
                            @endif

                            <!-- Actions box -->
                            <div class="card bg-light border-0 mb-3 shadow-none">
                                <div class="card-body p-3 text-center">
                                    <h6 class="fw-bold text-black mb-3"><i class="fa fa-cog me-1"></i> {{ __('Manage Status') }}</h6>
                                    
                                    @if($sellRequest->status !== 'contacted')
                                        <form action="{{ route('sell-requests.update-status', $sellRequest) }}" method="POST" class="mb-3">
                                            @csrf
                                            <input type="hidden" name="status" value="contacted">
                                            <div class="mb-2 text-start">
                                                <label class="form-label small mb-1">{{ __('Delivery Date') }} <span class="text-danger">*</span></label>
                                                <input type="month" name="delivery_date" class="form-control form-control-sm" value="{{ old('delivery_date', $sellRequest->delivery_date ? substr($sellRequest->delivery_date, 0, 7) : '') }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100 py-2 shadow-xs btn-sm">
                                                <i class="fa fa-check-circle me-2"></i> {{ __('Accept / Mark Contacted') }}
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-success mb-2 small fw-bold"><i class="fa fa-check-double me-1"></i> {{ __('Status: Contacted') }}</div>
                                        <form action="{{ route('sell-requests.update-status', $sellRequest) }}" method="POST" class="mb-3">
                                            @csrf
                                            <input type="hidden" name="status" value="contacted">
                                            <div class="mb-2 text-start">
                                                <label class="form-label small mb-1">{{ __('Update Delivery Date') }}</label>
                                                <input type="month" name="delivery_date" class="form-control form-control-sm" value="{{ old('delivery_date', $sellRequest->delivery_date ? substr($sellRequest->delivery_date, 0, 7) : '') }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-phoenix-success w-100 btn-sm">
                                                <i class="fa fa-calendar-check me-2"></i> {{ __('Save Delivery Date') }}
                                            </button>
                                        </form>
                                    @endif

                                    <div class="border-top my-3"></div>

                                    <h6 class="fw-bold text-black mb-3">{{ __('Visibility') }}</h6>
                                    @if($sellRequest->visibility === 'private')
                                        <form action="{{ route('sell-requests.update-visibility', $sellRequest) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to make this public?') }}')">
                                            @csrf
                                            <input type="hidden" name="visibility" value="public">
                                            <input type="hidden" name="type" value="buy">
                                            <button type="submit" class="btn btn-phoenix-primary btn-sm w-100 py-2">
                                                <i class="fa fa-globe me-2"></i> {{ __('Make Public') }}
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('sell-requests.update-visibility', $sellRequest) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="visibility" value="private">
                                            <button type="submit" class="btn btn-phoenix-secondary btn-sm w-100 py-2">
                                                <i class="fa fa-eye-slash me-2"></i> {{ __('Make Private') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Delete Request -->
                            <form action="{{ route('sell-requests.destroy', $sellRequest) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this request?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2">
                                    <i class="fa fa-trash me-2"></i> {{ __('Delete Request') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <a href="{{ route('sell-requests.show', $sellRequest) }}" class="btn btn-primary btn-sm"><i class="fa fa-external-link-alt me-1"></i>{{ __('Go to Page') }}</a>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Installment Request Modals -->
@foreach($user->buyAppartmentInstallments as $buyAppartmentInstallment)
    <div class="modal fade" id="installmentModal{{ $buyAppartmentInstallment->id }}" tabindex="-1" aria-labelledby="installmentModalLabel{{ $buyAppartmentInstallment->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-black" id="installmentModalLabel{{ $buyAppartmentInstallment->id }}">
                        <i class="fa fa-file-invoice-dollar text-primary me-2"></i>{{ __('Installment Request Details') }} #{{ $buyAppartmentInstallment->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Status Header block -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 p-3 bg-light rounded border border-light-subtle">
                        <div>
                            <span class="text-muted d-block small">{{ __('Created At') }}</span>
                            <span class="fw-bold text-black">{{ $buyAppartmentInstallment->created_at?->format('M d, Y H:i') }} <small class="text-muted">({{ $buyAppartmentInstallment->created_at?->diffForHumans() }})</small></span>
                        </div>
                        <div>
                            @if($buyAppartmentInstallment->status === 'contacted')
                                <span class="badge bg-success px-3 py-2 fs-9">{{ __('Contacted') }}</span>
                            @else
                                <span class="badge bg-warning px-3 py-2 fs-9">{{ __('Pending') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left Pane: Info -->
                        <div class="col-md-7 border-end">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa fa-sliders-h me-1"></i> {{ __('Installment Preferences') }}</h6>
                            
                            <div class="row g-2 mb-4">
                                <div class="col-md-3 col-6 text-center">
                                    <div class="p-3 border rounded bg-light">
                                        <i class="fa fa-calendar-alt text-primary mb-2 fs-5"></i>
                                        <h4 class="mb-0 text-black fw-bold">{{ $buyAppartmentInstallment->years_of_installment }}</h4>
                                        <small class="text-muted d-block font-size-xs">{{ __('Years') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 text-center">
                                    <div class="p-3 border rounded bg-light">
                                        <i class="fa fa-percentage text-primary mb-2 fs-5"></i>
                                        <h4 class="mb-0 text-black fw-bold">{{ $buyAppartmentInstallment->deposit_percetage }}%</h4>
                                        <small class="text-muted d-block font-size-xs">{{ __('Deposit') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 text-center">
                                    <div class="p-3 border rounded bg-light">
                                        <i class="fa fa-money-bill-wave text-primary mb-2 fs-5"></i>
                                        <h4 class="mb-0 text-black fw-bold" style="font-size: 1.05rem; line-height: 1.8;">{{ number_format($buyAppartmentInstallment->monthly_income) }}</h4>
                                        <small class="text-muted d-block font-size-xs">{{ __('Income (EGP)') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 text-center">
                                    <div class="p-3 border rounded bg-light">
                                        <i class="fa fa-wallet text-primary mb-2 fs-5"></i>
                                        <h4 class="mb-0 text-black fw-bold" style="font-size: 1.05rem; line-height: 1.8;">{{ $buyAppartmentInstallment->monthly_installment ? number_format($buyAppartmentInstallment->monthly_installment) : '-' }}</h4>
                                        <small class="text-muted d-block font-size-xs">{{ __('Desired Pay (EGP)') }}</small>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-primary mb-3"><i class="fa fa-info-circle me-1"></i> {{ __('Requester Information') }}</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('City') }}</small>
                                    <span class="fw-bold text-black">{{ $buyAppartmentInstallment->city }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block small">{{ __('Area') }}</small>
                                    <span class="fw-bold text-black">{{ $buyAppartmentInstallment->area }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block small">{{ __('Job Title') }}</small>
                                    <span class="fw-bold text-black">{{ $buyAppartmentInstallment->job_title }}</span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block small">{{ __('Age of Requester') }}</small>
                                    <span class="fw-bold text-black">{{ $buyAppartmentInstallment->age }} {{ __('Years') }}</span>
                                </div>
                            </div>

                            <!-- Requested Apartment -->
                            @if($buyAppartmentInstallment->apartment)
                                <div class="p-3 border border-primary rounded bg-light mb-4">
                                    <h6 class="text-primary fw-bold mb-2"><i class="fa fa-building me-1"></i> {{ __('Requested Apartment') }}</h6>
                                    <p class="text-black mb-3 small" style="line-height: 1.5;">{{ $buyAppartmentInstallment->apartment->description }}</p>
                                    <a href="{{ route('uptowns.show', $buyAppartmentInstallment->apartment_id) }}" class="btn btn-phoenix-primary btn-xs" target="_blank">
                                        {{ __('View Apartment Details') }} <i class="fa fa-external-link-alt ms-1"></i>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Right Pane: Docs & Actions -->
                        <div class="col-md-5 ps-md-4 mt-4 mt-md-0">
                            <h6 class="fw-bold text-primary mb-3"><i class="fa fa-file-contract me-1"></i> {{ __('Identity Documents') }}</h6>
                            
                            <div class="row g-2 mb-4">
                                <div class="col-6 text-center">
                                    <small class="text-muted d-block mb-1 small">{{ __('Front Image') }}</small>
                                    @if($buyAppartmentInstallment->identity_front_image)
                                        <a href="{{ $buyAppartmentInstallment->identity_front_image }}" target="_blank" class="d-block border rounded p-1 shadow-sm bg-white hover-zoom">
                                            <img src="{{ $buyAppartmentInstallment->identity_front_image }}" class="img-fluid rounded" alt="Front" style="max-height: 100px; object-fit: contain; width: 100%;">
                                        </a>
                                    @else
                                        <div class="p-3 border rounded bg-light text-muted small">{{ __('No Image') }}</div>
                                    @endif
                                </div>
                                <div class="col-6 text-center">
                                    <small class="text-muted d-block mb-1 small">{{ __('Back Image') }}</small>
                                    @if($buyAppartmentInstallment->identity_back_image)
                                        <a href="{{ $buyAppartmentInstallment->identity_back_image }}" target="_blank" class="d-block border rounded p-1 shadow-sm bg-white hover-zoom">
                                            <img src="{{ $buyAppartmentInstallment->identity_back_image }}" class="img-fluid rounded" alt="Back" style="max-height: 100px; object-fit: contain; width: 100%;">
                                        </a>
                                    @else
                                        <div class="p-3 border rounded bg-light text-muted small">{{ __('No Image') }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions box -->
                            <div class="card bg-light border-0 mb-4 shadow-none">
                                <div class="card-body p-3 text-center">
                                    <h6 class="fw-bold text-black mb-3"><i class="fa fa-cog me-1"></i> {{ __('Manage Status') }}</h6>
                                    
                                    @if($buyAppartmentInstallment->status !== 'contacted')
                                        <form action="{{ route('apartment-installments.update-status', $buyAppartmentInstallment) }}" method="POST" class="mb-0">
                                            @csrf
                                            <input type="hidden" name="status" value="contacted">
                                            <button type="submit" class="btn btn-success btn-sm w-100 py-2 shadow-xs">
                                                <i class="fa fa-check-circle me-2"></i> {{ __('Mark Contacted') }}
                                            </button>
                                        </form>
                                    @else
                                        <div class="text-success small fw-bold"><i class="fa fa-check-double me-1"></i> {{ __('Status: Contacted') }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Delete Request -->
                            <form action="{{ route('apartment-installments.destroy', $buyAppartmentInstallment) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this request?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2">
                                    <i class="fa fa-trash me-2"></i> {{ __('Delete Request') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <a href="{{ route('apartment-installments.show', $buyAppartmentInstallment) }}" class="btn btn-primary btn-sm"><i class="fa fa-external-link-alt me-1"></i>{{ __('Go to Page') }}</a>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
    .hover-zoom {
        overflow: hidden;
        display: block;
    }
    .hover-zoom img {
        transition: transform 0.2s ease-in-out;
    }
    .hover-zoom:hover img {
        transform: scale(1.06);
    }
    .btn-icon {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .nav-tabs-phoenix .nav-link {
        font-weight: 600;
        color: #525b75;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1rem;
    }
    .nav-tabs-phoenix .nav-link.active {
        color: #3874ff;
        border-bottom-color: #3874ff;
        background: transparent;
    }
    .table th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        color: #525b75;
    }
    .fs-9 {
        font-size: 0.75rem !important;
    }
</style>
@endsection
