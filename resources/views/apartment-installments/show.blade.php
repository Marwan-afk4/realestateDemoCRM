@extends('layouts.app')
@section('title', __('Mortgage Request Details'))
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('Mortgage Request Details') }} #{{ $buyAppartmentInstallment->id }}
            @if($buyAppartmentInstallment->status === 'contacted')
                <span class="badge bg-success fs-10 ms-2">{{ __('Contacted') }}</span>
            @else
                <span class="badge bg-warning fs-10 ms-2">{{ __('Pending') }}</span>
            @endif
        </h1>
        <a href="{{ route('apartment-installments.index') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> {{ __('Back to List') }}</a>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Mortgage Preferences') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3 text-center">
                            <div class="p-3 border rounded bg-light">
                                <i class="fa fa-calendar-alt text-primary mb-2 fs-5"></i>
                                <h4 class="mb-0">{{ $buyAppartmentInstallment->years_of_installment }}</h4>
                                <small class="text-muted">{{ __('Years of Mortgage') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 text-center">
                            <div class="p-3 border rounded bg-light">
                                <i class="fa fa-percentage text-primary mb-2 fs-5"></i>
                                <h4 class="mb-0">{{ $buyAppartmentInstallment->deposit_percetage }}%</h4>
                                <small class="text-muted">{{ __('Deposit Percentage') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 text-center">
                            <div class="p-3 border rounded bg-light">
                                <i class="fa fa-money-bill-wave text-primary mb-2 fs-5"></i>
                                <h4 class="mb-0">{{ number_format($buyAppartmentInstallment->monthly_income) }}</h4>
                                <small class="text-muted">{{ __('Monthly Income') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 text-center">
                            <div class="p-3 border rounded bg-light">
                                <i class="fa fa-wallet text-primary mb-2 fs-5"></i>
                                <h4 class="mb-0">{{ $buyAppartmentInstallment->monthly_installment ? number_format($buyAppartmentInstallment->monthly_installment) : '-' }}</h4>
                                <small class="text-muted">{{ __('Desired Installment') }}</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('City') }}</label>
                            <span class="fw-bold">{{ $buyAppartmentInstallment->city }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Area') }}</label>
                            <span class="fw-bold">{{ $buyAppartmentInstallment->area }}</span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small d-block">{{ __('Job Title') }}</label>
                            <span class="fw-bold">{{ $buyAppartmentInstallment->job_title }}</span>
                        </div>
                    </div>

                    @if($buyAppartmentInstallment->apartment)
                    <div class="mt-3 p-3 border rounded bg-light border-primary">
                        <label class="text-primary small d-block fw-bold mb-1">{{ __('Requested Apartment') }}</label>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $buyAppartmentInstallment->apartment->description }}</span>
                            <a href="{{ route('uptowns.show', $buyAppartmentInstallment->apartment_id) }}" class="btn btn-phoenix-primary btn-sm">
                                {{ __('View Apartment Details') }} <i class="fa fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-md-4">
            <!-- User Info -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('User Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar avatar-4xl mb-2">
                            <img src="/phoenix/assets/img/team/avatar.webp" class="rounded-circle border" alt="">
                        </div>
                        <h5 class="mb-0">{{ $buyAppartmentInstallment->user->first_name }} {{ $buyAppartmentInstallment->user->last_name }}</h5>
                        <p class="text-muted small mb-0">{{ $buyAppartmentInstallment->user->email }}</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">{{ __('Phone') }}</span>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-2">{{ $buyAppartmentInstallment->user->phone }}</span>
                                <a href="tel:{{ $buyAppartmentInstallment->user->phone }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-phone"></i>
                                </a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">{{ __('Age') }}</span>
                            <span class="fw-bold">{{ $buyAppartmentInstallment->age }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Documents -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Identity Documents') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="text-muted small d-block text-center">{{ __('Front') }}</label>
                            <a href="{{ $buyAppartmentInstallment->identity_front_image }}" target="_blank">
                                <img src="{{ $buyAppartmentInstallment->identity_front_image }}" class="img-fluid rounded border shadow-sm" alt="Identity Front">
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="text-muted small d-block text-center">{{ __('Back') }}</label>
                            <a href="{{ $buyAppartmentInstallment->identity_back_image }}" target="_blank">
                                <img src="{{ $buyAppartmentInstallment->identity_back_image }}" class="img-fluid rounded border shadow-sm" alt="Identity Back">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-body">
                    @if($buyAppartmentInstallment->status !== 'contacted')
                    <form action="{{ route('apartment-installments.update-status', $buyAppartmentInstallment) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="contacted">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-check-circle me-2"></i> {{ __('Mark as Contacted') }}
                        </button>
                    </form>
                    @endif
                    
                    <form action="{{ route('apartment-installments.destroy', $buyAppartmentInstallment) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fa fa-trash me-2"></i> {{ __('Delete Request') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
