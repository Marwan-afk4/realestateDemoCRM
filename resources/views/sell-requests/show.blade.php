@extends('layouts.app')
@section('title', __('Unit Request Details'))
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ __('Unit Request Details') }} #{{ $sellRequest->id }} 
            @if($sellRequest->status === 'contacted')
                <span class="badge bg-success fs-10 ms-2">{{ __('Contacted') }}</span>
            @else
                <span class="badge bg-warning fs-10 ms-2">{{ __('Pending') }}</span>
            @endif
        </h1>
        <a href="{{ route('sell-requests.index') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> {{ __('Back to List') }}</a>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Property Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block">{{ __('Unit Type') }}</label>
                            @if($sellRequest->uptown_type_id)
                                <a href="{{ route('uptown-types.show', $sellRequest->uptown_type_id) }}">
                                    <span class="badge bg-primary fs-9">{{ $sellRequest->uptownType?->name_en ?? ($sellRequest->uptownType?->name ?? '-') }}</span>
                                </a>
                            @else
                                <span class="badge bg-primary fs-9">{{ $sellRequest->uptownType?->name_en ?? ($sellRequest->uptownType?->name ?? '-') }}</span>
                            @endif
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block">{{ __('Sub Type') }}</label>
                            @if($sellRequest->unit_sub_type_id)
                                <a href="{{ route('unit-sub-types.show', $sellRequest->unit_sub_type_id) }}">
                                    <span class="badge bg-info fs-9 text-white">{{ $sellRequest->unitSubType?->name_en ?? '-' }}</span>
                                </a>
                            @else
                                <span class="badge bg-info fs-9 text-white">{{ $sellRequest->unitSubType?->name_en ?? '-' }}</span>
                            @endif
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block">{{ __('Price') }}</label>
                            <h4 class="text-primary mb-0">{{ number_format($sellRequest->price) }} <small class="text-muted fs-10">EGP</small></h4>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block">{{ __('Location') }}</label>
                            <span>{{ $sellRequest->country }}, {{ $sellRequest->city }}, {{ $sellRequest->area }}</span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong><label class="text-muted small d-block">{{ __('Execution Date') }}</label></strong>
                            <h3 class="text-success fw-bold mb-0">
                                <i class="fa fa-calendar-alt text-success me-1"></i>
                                {{ $sellRequest->execution_date?->label() ?? '-' }}
                            </h3>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong><label class="text-muted small d-block">{{ __('Delivery Date') }}</label></strong>
                            <h3 class="text-primary fw-bold mb-0">
                                <i class="fa fa-truck text-primary me-1"></i>
                                {{ $sellRequest->delivery_date_label ?? '-' }}
                            </h3>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3 mb-3 text-center">
                            <div class="p-2 border rounded bg-light">
                                <i class="fa fa-door-open text-primary mb-1 d-block"></i>
                                <span class="d-block fw-bold">{{ $sellRequest->rooms_no ?? '-' }}</span>
                                <small class="text-muted">{{ __('Rooms') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 text-center">
                            <div class="p-2 border rounded bg-light">
                                <i class="fa fa-bath text-primary mb-1 d-block"></i>
                                <span class="d-block fw-bold">{{ $sellRequest->bathrooms_no ?? '-' }}</span>
                                <small class="text-muted">{{ __('Bathrooms') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 text-center">
                            <div class="p-2 border rounded bg-light">
                                <i class="fa fa-vector-square text-primary mb-1 d-block"></i>
                                <span class="d-block fw-bold">{{ $sellRequest->space ?? '-' }} <small>m2</small></span>
                                <small class="text-muted">{{ __('Space') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 text-center">
                            <div class="p-2 border rounded bg-light">
                                <i class="fa fa-building text-primary mb-1 d-block"></i>
                                <span class="d-block fw-bold">{{ $sellRequest->floor_no ?? '-' }}</span>
                                <small class="text-muted">{{ __('Floor') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Developer') }}</label>
                            @if($sellRequest->developer)
                                <a href="{{ route('developers.show', $sellRequest->developer_id) }}" class="fw-bold">{{ $sellRequest->developer->name }}</a>
                            @else
                                <span>-</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Compound') }}</label>
                            @if($sellRequest->compound)
                                <a href="{{ route('compounds.show', $sellRequest->compound_id) }}" class="fw-bold">{{ $sellRequest->compound->compound_name }}</a>
                            @else
                                <span>-</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Garden Area') }}</label>
                            <span>{{ $sellRequest->garden_area ? __('Yes') : __('No') }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Finishing') }}</label>
                            <span>
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
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block">{{ __('Execution Date') }}</label>
                            <h3 class="text-primary fw-bold mb-0">
                                <i class="fa fa-calendar-alt text-primary me-1"></i>
                                {{ $sellRequest->execution_date?->label() ?? '-' }}
                            </h3>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">{{ __('Delivery Date') }}</label>
                            <span>{{ $sellRequest->delivery_date_label ?? '-' }}</span>
                        </div>
                    </div>

                    @if($sellRequest->notes)
                    <div class="mt-3">
                        <label class="text-muted small d-block">{{ __('Notes') }}</label>
                        <div class="p-3 border rounded bg-light italic">
                            "{{ $sellRequest->notes }}"
                        </div>
                    </div>
                    @endif

                    @if($sellRequest->images->count() > 0)
                    <div class="mt-4">
                        <label class="text-muted small d-block mb-2">{{ __('Unit Images') }}</label>
                        <div class="row g-2">
                            @foreach($sellRequest->images as $img)
                            <div class="col-md-3 mb-2">
                                <label class="small text-muted d-block">{{ ucfirst(str_replace('_', ' ', $img->key)) }}</label>
                                <a href="{{ asset('storage/' . $img->image) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $img->image) }}" class="img-fluid rounded border" style="height: 120px; width: 100%; object-fit: cover;" alt="{{ $img->key }}">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($sellRequest->installments)
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">{{ __('Installment Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small d-block">{{ __('Total Installment Price') }}</label>
                            <span class="fw-bold">{{ number_format($sellRequest->installments_total_price) }}</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small d-block">{{ __('Years Left') }}</label>
                            <span class="fw-bold">{{ $sellRequest->installments_years_left }} / {{ $sellRequest->installments_years }}</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small d-block">{{ __('Price Per Year') }}</label>
                            <span class="fw-bold">{{ number_format($sellRequest->installments_price_per_year) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(!empty($sellRequest->extra_data))
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Additional Data') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($sellRequest->extra_data as $key => $value)
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small d-block">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                            <span class="fw-bold">{{ is_array($value) ? implode(', ', $value) : ($value === true ? 'Yes' : ($value === false ? 'No' : $value)) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
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
                        <h5 class="mb-0">{{ $sellRequest->user->first_name.' '.$sellRequest->user->last_name }}</h5>
                        <p class="text-muted small mb-0">{{ $sellRequest->user->email }}</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">{{ __('Phone') }}</span>
                            <span class="fw-bold">{{ $sellRequest->user->phone }}</span>
                            <a href="tel:{{ $sellRequest->user->phone }}" class="btn btn-sm btn-success">
                                <i class="fa fa-phone"></i> {{ __('Call') }}
                            </a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">{{ __('Age') }}</span>
                            <span class="fw-bold">{{ $sellRequest->age }}</span>
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
                            <a href="{{ $sellRequest->identity_front_image }}" target="_blank">
                                <img src="{{ $sellRequest->identity_front_image }}" class="img-fluid rounded border shadow-sm" alt="Identity Front">
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="text-muted small d-block text-center">{{ __('Back') }}</label>
                            <a href="{{ $sellRequest->identity_back_image }}" target="_blank">
                                <img src="{{ $sellRequest->identity_back_image }}" class="img-fluid rounded border shadow-sm" alt="Identity Back">
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="text-muted small d-block mb-1">{{ __('Property PDF') }}</label>
                        @if($sellRequest->detailed_pdf)
                            <a href="{{ $sellRequest->detailed_pdf }}" target="_blank" class="btn btn-phoenix-primary w-100">
                                <i class="fa fa-file-pdf me-2"></i> {{ __('View Detailed PDF') }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('No PDF attached') }}</span>
                        @endif
                    </div>
                    <div class="mt-3">
                        <label class="text-muted small d-block mb-1">{{ __('Video') }}</label>
                        @if($sellRequest->video)
                            <video controls class="w-100 rounded border" style="max-height: 280px; background: #000;">
                                <source src="{{ $sellRequest->video }}">
                                {{ __('Your browser does not support the video tag.') }}
                            </video>
                            <a href="{{ $sellRequest->video }}" target="_blank" class="btn btn-phoenix-secondary btn-sm w-100 mt-2">
                                <i class="fa fa-external-link-alt me-1"></i> {{ __('Open Video') }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('No video attached') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-body">
                    <label class="text-muted small d-block mb-2">{{ __('Manage Status') }}</label>
                    @if($sellRequest->status !== 'contacted')
                    <form action="{{ route('sell-requests.update-status', $sellRequest) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="contacted">
                        <div class="mb-2">
                            <label class="form-label small mb-1">{{ __('Delivery Date') }} <span class="text-danger">*</span></label>
                            <input type="month" name="delivery_date" class="form-control form-control-sm" value="{{ old('delivery_date', $sellRequest->delivery_date ? substr($sellRequest->delivery_date, 0, 7) : '') }}" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-check-circle me-2"></i> {{ __('Accept / Mark as Contacted') }}
                        </button>
                    </form>
                    @else
                    <form action="{{ route('sell-requests.update-status', $sellRequest) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="contacted">
                        <div class="mb-2">
                            <label class="form-label small mb-1">{{ __('Update Delivery Date') }}</label>
                            <input type="month" name="delivery_date" class="form-control form-control-sm" value="{{ old('delivery_date', $sellRequest->delivery_date ? substr($sellRequest->delivery_date, 0, 7) : '') }}" required>
                        </div>
                        <button type="submit" class="btn btn-phoenix-success w-100">
                            <i class="fa fa-calendar-check me-2"></i> {{ __('Save Delivery Date') }}
                        </button>
                    </form>
                    @endif

                    <hr>

                    <label class="text-muted small d-block mb-2">{{ __('Visibility') }}</label>
                    @if($sellRequest->visibility === 'private')
                    <form action="{{ route('sell-requests.update-visibility', $sellRequest) }}" method="POST" class="mb-2" onsubmit="return confirm('{{ __('Making this request public will create a property listing in the Units section. Are you sure?') }}')">
                        @csrf
                        <input type="hidden" name="visibility" value="public">
                        
                        <div class="mb-2">
                            <label class="form-label small mb-1">{{ __('Property Purpose') }}</label>
                            <select name="type" class="form-select form-select-sm" required>
                                <option value="buy">{{ __('Buy') }}</option>
                                <option value="rent">{{ __('Rent') }}</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-phoenix-primary w-100">
                            <i class="fa fa-globe me-2"></i> {{ __('Make Public') }}
                        </button>
                    </form>
                    @else
                    <form action="{{ route('sell-requests.update-visibility', $sellRequest) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="visibility" value="private">
                        <button type="submit" class="btn btn-phoenix-secondary w-100">
                            <i class="fa fa-eye-slash me-2"></i> {{ __('Make Private') }}
                        </button>
                    </form>
                    @endif

                    <hr>

                    <button class="btn btn-primary w-100 mb-2" onclick="window.print()">
                        <i class="fa fa-print me-2"></i> {{ __('Print Request') }}
                    </button>
                    <form action="{{ route('sell-requests.destroy', $sellRequest) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this request?') }}')">
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

<style>
    @media print {
        .navbar-vertical, .navbar-top, .btn, .search-wrapper, .card-header button {
            display: none !important;
        }
        .main {
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>
@endsection
