@extends('layouts.app')
@php $currentPage = 'admins'; @endphp
@section('title', __('Edit Admin'))
@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">{{ __('Edit Admin') }}: <span class="text-primary">{{ $adminUser->full_name }}</span></h1>
        <a href="{{ route('admin-users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin-users.update', $adminUser) }}" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('First Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', $adminUser->first_name) }}" required>
                        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name', $adminUser->last_name) }}" required>
                        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $adminUser->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Phone') }} <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $adminUser->phone) }}" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('New Password') }}
                            <small class="text-muted fw-normal">({{ __('leave blank to keep current') }})</small>
                        </label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="">{{ __('-- Select Status --') }}</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ old('status', $adminUser->status->value ?? '') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">{{ __('Assigned Role') }} <span class="text-danger">*</span></label>
                        <div class="row g-2 mt-1">
                            @foreach($roles as $role)
                            @php $isSelected = old('role_id') ? old('role_id') == $role->id : ($adminRole && $adminRole->id == $role->id); @endphp
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <label class="role-card d-flex align-items-center gap-2 p-3 rounded border {{ $isSelected ? 'selected' : '' }}"
                                       style="cursor:pointer;transition:all .2s;" for="role_{{ $role->id }}">
                                    <input type="radio" name="role_id" id="role_{{ $role->id }}"
                                           value="{{ $role->id }}"
                                           class="form-check-input mt-0 role-radio"
                                           {{ $isSelected ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-semibold small">{{ ucfirst($role->name) }}</div>
                                        <div class="text-muted" style="font-size:.7rem;">
                                            {{ $role->permissions->count() }} {{ __('permissions') }}
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('role_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save me-1"></i> {{ __('Save Changes') }}
                    </button>
                    <a href="{{ route('admin-users.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.role-card { background:#f8fafc; border-color:#e2e8f0 !important; }
.role-card:hover { background:#eef2ff; border-color:#818cf8 !important; }
.role-card.selected { background:#eef2ff; border-color:#6366f1 !important; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.role-radio').forEach(radio => {
        radio.addEventListener('change', function(){
            document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
            this.closest('.role-card').classList.add('selected');
        });
    });
});
</script>
@endsection
