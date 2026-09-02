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
                <th>{{ __('Complainant') }}</th>
                <th>{{ __('Contact') }}</th>
                <th>{{ __('Message') }}</th>
                <th>{{ __('Status') }}</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                        {{ __('Submitted At') }}
                        @if(request('sort') === 'created_at')
                            <i class="fas fa-sort-{{ request('order') === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </a>
                </th>
                <th class="text-center">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $complaint)
                <tr>
                    <td>{{ $complaint->id }}</td>
                    <td>
                        <div>
                            <strong>{{ $complaint->name }}</strong>
                            @if($complaint->user)
                                <br>
                                <strong>
                                    {{ $complaint->user->full_name }}
                                </strong>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div>
                            <i class="fas fa-phone text-muted"></i> {{ $complaint->user->phone }}
                            @if($complaint->user && $complaint->user->email)
                                <br>
                                <i class="fas fa-envelope text-muted"></i><a href="mailto:{{ $complaint->user->email }}">{{ $complaint->user->email }}</a>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="max-width: 300px;">
                            <p class="mb-0">{{ Str::limit($complaint->message, 100) }}</p>
                            @if(strlen($complaint->message) > 100)
                                <button class="btn btn-link btn-sm p-0"
                                        data-bs-toggle="modal"
                                        data-bs-target="#messageModal"
                                        data-message="{{ $complaint->message }}"
                                        data-complainant="{{ $complaint->name }}">
                                    {{ __('Read more...') }}
                                </button>
                            @endif
                        </div>
                    </td>
                    <td>
                        @switch($complaint->status)
                            @case('open')
                                <span class="badge bg-warning">{{ __('Open') }}</span>
                                @break
                            @case('closed')
                                <span class="badge bg-success">{{ __('Closed') }}</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ ucfirst($complaint->status) }}</span>
                        @endswitch
                    </td>
                    <td>{{ $complaint->created_at->format('M j, Y H:i') }}</td>
                    <td class="text-center">
                        <div class="action-buttons">
                            @if($complaint->status === 'open')
                                <form action="{{ route('requests.complaint.close', $complaint->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to close this complaint?') }}')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> {{ __('Close') }}
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">{{ __('Closed') }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>{{ __('No complaints found') }}</p>
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

{{-- Message Modal --}}
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">{{ __('Complaint Message') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>{{ __('From') }}:</strong> <span id="modalComplainant"></span>
                </div>
                <div>
                    <strong>{{ __('Message') }}:</strong>
                    <p id="modalMessage" class="mt-2"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageModal = document.getElementById('messageModal');
    if (messageModal) {
        messageModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const message = button.getAttribute('data-message');
            const complainant = button.getAttribute('data-complainant');

            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modalComplainant').textContent = complainant;
        });
    }
});
</script>
@endpush
