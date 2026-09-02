<div class="container-fluid">
    <style>
        .lead-card {
            transition: all 0.2s ease;
        }
        .lead-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .table-responsive {
            border-radius: 0.375rem;
        }
        .badge {
            font-size: 0.75em;
        }
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
    {{-- Success Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Error Messages --}}
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fa fa-users fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ count($leads) }}</h4>
                    <small>{{ __('Available Leads') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 bg-success text-white">
                <div class="card-body text-center">
                    <i class="fa fa-user-check fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $brokerLeads->count() }}</h4>
                    <small>{{ __('Assigned Leads') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Available Leads Section --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fa fa-users me-2"></i>{{ __('Available Leads') }}
                        <span class="badge bg-light text-primary ms-2">{{ count($leads) }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Search Bar --}}
                    <div class="mb-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="{{ __('Search by name or phone...') }}"
                                   wire:model.live.debounce.300ms="search">
                            @if($search)
                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                                    <i class="fa fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Assignment Form --}}
                    @if(!empty($leads))
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <select wire:model="lead_id" class="form-select form-select-sm">
                                    <option value="">{{ __('Select a lead to assign...') }}</option>
                                    @foreach($availableLeads as $lead)
                                        <option value="{{ $lead->id }}">
                                            {{ $lead->lead_name }} - {{ $lead->lead_phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <button wire:click="assignLead" class="btn btn-success btn-sm w-100"
                                        wire:loading.attr="disabled"
                                        @if(!$this->canAssign) disabled @endif>
                                    <span wire:loading.remove wire:target="assignLead">
                                        <i class="fa fa-plus"></i> {{ __('Assign') }}
                                    </span>
                                    <span wire:loading wire:target="assignLead">
                                        <i class="fa fa-spinner fa-spin"></i> {{ __('Assigning...') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Available Leads List --}}
                    @if($availableLeads && $availableLeads->count() > 0)
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="text-start">{{ __('Name') }}</th>
                                        <th class="text-start">{{ __('Phone') }}</th>
                                        <th class="text-center">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableLeads as $lead)
                                        <tr class="@if($lead_id == $lead->id) table-primary @endif">
                                            <td class="text-start">
                                                <strong>{{ $lead->lead_name }}</strong>
                                            </td>
                                            <td class="text-start">
                                                <span class="text-muted">{{ $lead->lead_phone }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    @if($lead_id == $lead->id)
                                                        <span class="badge bg-primary me-1">
                                                            <i class="fa fa-check"></i> {{ __('Selected') }}
                                                        </span>
                                                        <button wire:click="assignLead"
                                                                class="btn btn-success btn-sm"
                                                                wire:loading.attr="disabled">
                                                            <span wire:loading.remove wire:target="assignLead">
                                                                <i class="fa fa-plus"></i>
                                                            </span>
                                                            <span wire:loading wire:target="assignLead">
                                                                <i class="fa fa-spinner fa-spin"></i>
                                                            </span>
                                                        </button>
                                                    @else
                                                        <button wire:click="$set('lead_id', '{{ $lead->id }}')"
                                                                class="btn btn-outline-primary btn-sm me-1">
                                                            <i class="fa fa-hand-pointer"></i> {{ __('Select') }}
                                                        </button>
                                                        <button wire:click="quickAssignLead({{ $lead->id }})"
                                                                class="btn btn-success btn-sm"
                                                                wire:loading.attr="disabled"
                                                                title="{{ __('Quick Assign') }}">
                                                            <span wire:loading.remove wire:target="quickAssignLead({{ $lead->id }})">
                                                                <i class="fa fa-plus"></i>
                                                            </span>
                                                            <span wire:loading wire:target="quickAssignLead({{ $lead->id }})">
                                                                <i class="fa fa-spinner fa-spin"></i>
                                                            </span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-search fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">
                                @if($search)
                                    {{ __('No leads found matching your search.') }}
                                @else
                                    {{ __('No available leads to assign.') }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Assigned Leads Section --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fa fa-user-check me-2"></i>{{ __('Assigned Leads') }}
                        <span class="badge bg-light text-success ms-2">{{ $brokerLeads->count() }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Search Bar for Assigned Leads --}}
                    <div class="mb-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="{{ __('Search assigned leads...') }}"
                                   wire:model.live.debounce.300ms="searchAssigned">
                            @if($searchAssigned)
                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('searchAssigned', '')">
                                    <i class="fa fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Assigned Leads Table --}}
                    @if ($brokerLeads->isEmpty())
                        <div class="text-center py-4">
                            <i class="fa fa-users fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">
                                @if($searchAssigned)
                                    {{ __('No assigned leads found matching your search.') }}
                                @else
                                    {{ __('No leads assigned to this broker yet.') }}
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="text-start">{{ __('Lead Info') }}</th>
                                        <th class="text-center">{{ __('End Date') }}</th>
                                        <th class="text-center">{{ __('Status') }}</th>
                                        <th class="text-center">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brokerLeads as $brokerLead)
                                        <tr>
                                            <td class="text-start">
                                                <div>
                                                    <strong class="d-block">{{ $brokerLead->lead->lead_name }}</strong>
                                                    <small class="text-muted">
                                                        <i class="fa fa-phone me-1"></i>{{ $brokerLead->lead->lead_phone }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <input type="date" class="form-control form-control-sm"
                                                    value="{{ $brokerLead->brocker_end_date }}"
                                                    wire:change="updateEndDate({{ $brokerLead->id }}, $event.target.value)"
                                                    wire:loading.attr="disabled"
                                                    title="{{ __('Set end date (optional)') }}"
                                                    style="min-width: 140px;">
                                            </td>
                                            <td class="text-center">
                                                <select class="form-select form-select-sm" 
                                                    wire:change="updateLeadStatus({{ $brokerLead->id }}, $event.target.value)"
                                                    wire:loading.attr="disabled"
                                                    style="min-width: 120px; background-color: #{{ $brokerLead->lead->status->color() }}; color: #{{ $brokerLead->lead->status->textColor() }}; border: none; font-weight: 500;">
                                                    <option value="pending" {{ $brokerLead->lead->status->value == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                    <option value="done" {{ $brokerLead->lead->status->value == 'done' ? 'selected' : '' }}>{{ __('Done') }}</option>
                                                    <option value="lost" {{ $brokerLead->lead->status->value == 'lost' ? 'selected' : '' }}>{{ __('Lost') }}</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button wire:click="unassignLead({{ $brokerLead->id }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    wire:confirm="{{ __('Are you sure you want to unassign this lead?') }}"
                                                    wire:loading.attr="disabled"
                                                    title="{{ __('Unassign Lead') }}">
                                                    <span wire:loading.remove wire:target="unassignLead({{ $brokerLead->id }})">
                                                        <i class="fa fa-times"></i>
                                                    </span>
                                                    <span wire:loading wire:target="unassignLead({{ $brokerLead->id }})">
                                                        <i class="fa fa-spinner fa-spin"></i>
                                                    </span>
                                                </button>
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
