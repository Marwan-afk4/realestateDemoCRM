<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        {{ __('ID') }}
                        @if(request('sort') === 'id')
                            <i class="fas fa-sort-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th>{{ __('Applicant') }}</th>
                <th>{{ __('Contact') }}</th>
                <th>{{ __('Qualification') }}</th>
                <th>{{ __('Experience') }}</th>
                <th>{{ __('Status') }}</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        {{ __('Applied At') }}
                        @if(request('sort') === 'created_at')
                            <i class="fas fa-sort-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $training)
                <tr>
                    <td>{{ $training->id }}</td>
                    <td>
                        <div>
                            <strong>{{ $training->full_name }}</strong>
                            <br>
                            <small class="text-muted">{{ __('Age') }}: {{ $training->age }}</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <i class="fas fa-envelope text-muted"></i> {{ $training->email }}
                            <br>
                            <i class="fas fa-phone text-muted"></i> {{ $training->phone }}
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $training->qualification }}</span>
                        <br>
                        <small class="text-muted">{{ $training->governate }}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $training->experience_year }} {{ __('years') }}</span>
                    </td>
                    <td>
                        @switch($training->status)
                            @case('pending')
                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @break
                            @case('approved')
                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                @if($training->training_period)
                                    <br><small class="text-muted">{{ $training->training_period }} {{ __('days') }}</small>
                                @endif
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                @break
                            @case('completed')
                                <span class="badge bg-primary">{{ __('Completed') }}</span>
                                @break
                        @endswitch
                    </td>
                    <td>{{ $training->created_at->format('M j, Y') }}</td>
                    <td class="text-center">
                        <div class="action-buttons">
                            @if($training->status === 'pending')
                                <button class="btn btn-success btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveTrainingModal"
                                        data-training-id="{{ $training->id }}"
                                        data-training-name="{{ $training->full_name }}">
                                    <i class="fas fa-check"></i> {{ __('Approve') }}
                                </button>
                                <form action="{{ route('requests.training.reject', $training->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to reject this training request?') }}')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> {{ __('Reject') }}
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">{{ __('No actions available') }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>{{ __('No training requests found') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($data->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $data->appends(request()->query())->links('pagination::custom') }}
    </div>
@endif
