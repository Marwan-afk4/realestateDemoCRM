<div class="container-fluid">
    <style>
        .broker-card {
            transition: all 0.2s ease;
        }
        .broker-card:hover {
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
        .disabled-section {
            opacity: 0.6;
            pointer-events: none;
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

    <div class="row">
        {{-- Available Brokers Section --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fa fa-users me-2"></i>{{ __('Available Brokers') }}
                        <span class="badge bg-light text-primary ms-2">{{ count($availableBrokers) }}</span>
                    </h6>
                </div>
                <div class="card-body {{ $hasActiveAssignment ? 'disabled-section' : '' }}">
                    @if($hasActiveAssignment)
                        <div class="alert alert-warning mb-3">
                            <i class="fa fa-lock me-2"></i>
                            {{ __('A broker is currently assigned to this lead. Please remove the current assignment before assigning a new broker.') }}
                        </div>
                    @endif

                    {{-- Search Bar --}}
                    <div class="mb-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="{{ __('Search by name, email or phone...') }}"
                                   wire:model.live.debounce.300ms="search"
                                   @if($hasActiveAssignment) disabled @endif>
                            @if($search)
                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')"
                                        @if($hasActiveAssignment) disabled @endif>
                                    <i class="fa fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Assignment Form --}}
                    @if(!empty($availableBrokers))
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <select wire:model="brocker_id" class="form-select form-select-sm"
                                        @if($hasActiveAssignment) disabled @endif>
                                    <option value="">{{ __('Select a broker to assign...') }}</option>
                                    @foreach($availableBrokers as $broker)
                                        <option value="{{ $broker->id }}">
                                            {{ $broker->user->full_name }} - {{ $broker->user->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <button wire:click="assignBroker" class="btn btn-success btn-sm w-100"
                                        wire:loading.attr="disabled"
                                        @if(!$canAssign) disabled @endif>
                                    <span wire:loading.remove wire:target="assignBroker">
                                        <i class="fa fa-plus"></i> {{ __('Assign') }}
                                    </span>
                                    <span wire:loading wire:target="assignBroker">
                                        <i class="fa fa-spinner fa-spin"></i> {{ __('Assigning...') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Available Brokers List --}}
                    @if($availableBrokers && $availableBrokers->count() > 0)
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
                                    @foreach($availableBrokers as $broker)
                                        <tr class="@if($brocker_id == $broker->id) table-primary @endif">
                                            <td class="text-start">
                                                <strong>{{ $broker->user->full_name }}</strong>
                                            </td>
                                            <td class="text-start">
                                                <span class="text-muted">{{ $broker->user->phone }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    @if($brocker_id == $broker->id)
                                                        <span class="badge bg-primary me-1">
                                                            <i class="fa fa-check"></i> {{ __('Selected') }}
                                                        </span>
                                                        <button wire:click="assignBroker"
                                                                class="btn btn-success btn-sm"
                                                                wire:loading.attr="disabled"
                                                                @if($hasActiveAssignment) disabled @endif>
                                                            <span wire:loading.remove wire:target="assignBroker">
                                                                <i class="fa fa-plus"></i>
                                                            </span>
                                                            <span wire:loading wire:target="assignBroker">
                                                                <i class="fa fa-spinner fa-spin"></i>
                                                            </span>
                                                        </button>
                                                    @else
                                                        <button wire:click="$set('brocker_id', '{{ $broker->id }}')"
                                                                class="btn btn-outline-primary btn-sm me-1"
                                                                @if($hasActiveAssignment) disabled @endif>
                                                            <i class="fa fa-hand-pointer"></i> {{ __('Select') }}
                                                        </button>
                                                        <button wire:click="quickAssignBroker({{ $broker->id }})"
                                                                class="btn btn-success btn-sm"
                                                                wire:loading.attr="disabled"
                                                                title="{{ __('Quick Assign') }}"
                                                                @if($hasActiveAssignment) disabled @endif>
                                                            <span wire:loading.remove wire:target="quickAssignBroker({{ $broker->id }})">
                                                                <i class="fa fa-plus"></i>
                                                            </span>
                                                            <span wire:loading wire:target="quickAssignBroker({{ $broker->id }})">
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
                                    {{ __('No brokers found matching your search.') }}
                                @else
                                    {{ __('No available brokers.') }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Assigned Brokers History Section --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fa fa-history me-2"></i>{{ __('Assignment History') }}
                        <span class="badge bg-light text-success ms-2">{{ $leadBrokers->count() }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Search Bar for History --}}
                    <div class="mb-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="{{ __('Search history...') }}"
                                   wire:model.live.debounce.300ms="searchAssigned">
                            @if($searchAssigned)
                                <button class="btn btn-outline-secondary" type="button" wire:click="$set('searchAssigned', '')">
                                    <i class="fa fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- History Table --}}
                    @if ($leadBrokers->isEmpty())
                        <div class="text-center py-4">
                            <i class="fa fa-history fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">
                                @if($searchAssigned)
                                    {{ __('No history found matching your search.') }}
                                @else
                                    {{ __('No assignment history for this lead.') }}
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="text-start">{{ __('Broker Info') }}</th>
                                        <th class="text-center">{{ __('End Date') }}</th>
                                        <th class="text-center">{{ __('Status') }}</th>
                                        <th class="text-center">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leadBrokers as $brokerLead)
                                        <tr class="@if($lead->brocker_id == $brokerLead->brocker_id) table-success @endif">
                                            <td class="text-start">
                                                <div>
                                                    <strong class="d-block">{{ $brokerLead->brocker->user->full_name }}</strong>
                                                    <small class="text-muted">
                                                        <i class="fa fa-phone me-1"></i>{{ $brokerLead->brocker->user->phone }}
                                                    </small>
                                                    @if($lead->brocker_id == $brokerLead->brocker_id)
                                                        <span class="badge bg-success mt-1">{{ __('Active') }}</span>
                                                    @endif
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
                                                    wire:change="updateLeadStatus($event.target.value)"
                                                    wire:loading.attr="disabled"
                                                    style="min-width: 120px; background-color: #{{ $lead->status->color() }}; color: #{{ $lead->status->textColor() }}; border: none; font-weight: 500;">
                                                    <option value="pending" {{ $lead->status->value == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                    <option value="done" {{ $lead->status->value == 'done' ? 'selected' : '' }}>{{ __('Done') }}</option>
                                                    <option value="lost" {{ $lead->status->value == 'lost' ? 'selected' : '' }}>{{ __('Lost') }}</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                @if($lead->brocker_id == $brokerLead->brocker_id)
                                                    <button wire:click="removeAssignment({{ $brokerLead->id }})"
                                                        class="btn btn-sm btn-outline-danger"
                                                        wire:confirm="{{ __('Are you sure you want to remove this assignment? This will unassign the broker from the lead.') }}"
                                                        wire:loading.attr="disabled"
                                                        title="{{ __('Unassign Broker') }}">
                                                        <span wire:loading.remove wire:target="removeAssignment({{ $brokerLead->id }})">
                                                            <i class="fa fa-user-times"></i>
                                                        </span>
                                                        <span wire:loading wire:target="removeAssignment({{ $brokerLead->id }})">
                                                            <i class="fa fa-spinner fa-spin"></i>
                                                        </span>
                                                    </button>
                                                @else
                                                     <button wire:click="removeAssignment({{ $brokerLead->id }})"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        wire:confirm="{{ __('Are you sure you want to delete this history record?') }}"
                                                        wire:loading.attr="disabled"
                                                        title="{{ __('Delete Record') }}">
                                                        <span wire:loading.remove wire:target="removeAssignment({{ $brokerLead->id }})">
                                                            <i class="fa fa-trash"></i>
                                                        </span>
                                                        <span wire:loading wire:target="removeAssignment({{ $brokerLead->id }})">
                                                            <i class="fa fa-spinner fa-spin"></i>
                                                        </span>
                                                    </button>
                                                @endif
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
