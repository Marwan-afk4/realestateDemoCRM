{{-- Approve Training Modal --}}
<div class="modal fade" id="approveTrainingModal" tabindex="-1" aria-labelledby="approveTrainingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveTrainingForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="approveTrainingModalLabel">{{ __('Approve Training Request') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p>{{ __('You are about to approve the training request for:') }}</p>
                        <strong id="trainingApplicantName"></strong>
                    </div>

                    <div class="mb-3">
                        <label for="training_period" class="form-label">{{ __('Training Period (Days)') }} <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control"
                               id="training_period"
                               name="training_period"
                               min="1"
                               max="365"
                               value="30"
                               required>
                        <div class="form-text">{{ __('Enter the number of days for the training period (1-365 days)') }}</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Once approved, the applicant will be notified and the training period will begin.') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> {{ __('Approve Training') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const approveModal = document.getElementById('approveTrainingModal');
    const approveForm = document.getElementById('approveTrainingForm');
    const applicantName = document.getElementById('trainingApplicantName');

    if (approveModal) {
        approveModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const trainingId = button.getAttribute('data-training-id');
            const trainingName = button.getAttribute('data-training-name');

            // Update form action
            approveForm.action = `{{ route('requests.index') }}/training/${trainingId}/approve`;

            // Update applicant name
            applicantName.textContent = trainingName;
        });
    }
});
</script>
@endpush
