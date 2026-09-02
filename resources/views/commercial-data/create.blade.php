@extends('layouts.app')
@section('title', __('Create Commercial Data Field'))
@section('content')
<div class="container-fluid">
    <h1 class="mb-3">{{ __('Create Commercial Data Field') }}</h1>
    <div class='main-card mb-3 card'>
        <div class='card-body'>
            <form action="{{ route('commercial-data.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Field Name (internal)') }}</label>
                        <input type="text" name="field_name" class="form-control" required placeholder="e.g., space">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Label (English)') }}</label>
                        <input type="text" name="label_en" class="form-control" required placeholder="e.g., Space (m2)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Label (Arabic)') }}</label>
                        <input type="text" name="label_ar" class="form-control" placeholder="e.g., المساحة">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Type') }}</label>
                        <select name="type" class="form-select" required>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="boolean">Boolean (Checkbox)</option>
                            <option value="select">Select (Dropdown)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Is Required?') }}</label>
                        <select name="is_required" class="form-select" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3" id="options-container" style="display: none;">
                        <label class="form-label">{{ __('Select Options (One per line)') }}</label>
                        <textarea name="options" class="form-control" rows="5" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                        <small class="text-muted">{{ __('Only required if type is "Select". Enter each option on a new line.') }}</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('commercial-data.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        const optionsContainer = document.getElementById('options-container');
        if (this.value === 'select') {
            optionsContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
        }
    });
</script>
@endsection
