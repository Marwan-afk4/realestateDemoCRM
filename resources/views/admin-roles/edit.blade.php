@extends('layouts.app')
@php $currentPage = 'roles'; @endphp
@section('title', __('Edit Role'))
@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">{{ __('Edit Role') }}: <span class="text-primary">{{ $adminRole->name }}</span></h1>
        <a href="{{ route('admin-roles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin-roles.update', $adminRole) }}">
                @csrf
                @method('PUT')

                {{-- Role Name --}}
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">{{ __('Role Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $adminRole->name) }}"
                           {{ $adminRole->name === 'super-admin' ? 'readonly' : '' }}
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($adminRole->name === 'super-admin')
                        <div class="form-text text-warning">
                            <i class="fa fa-lock me-1"></i> {{ __('The super-admin role name cannot be changed.') }}
                        </div>
                    @endif
                </div>

                {{-- Permissions Grid --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <label class="form-label fw-semibold mb-0">{{ __('Permissions') }}</label>
                        <div class="d-flex gap-2">
                            <button type="button" id="selectAll" class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-check-double me-1"></i> {{ __('Select All') }}
                            </button>
                            <button type="button" id="deselectAll" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-times me-1"></i> {{ __('Deselect All') }}
                            </button>
                        </div>
                    </div>

                    <div class="row g-2">
                        @foreach($permissions as $permission)
                        @php $checked = in_array($permission->id, old('permissions', $rolePermissions)); @endphp
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <label class="permission-card d-flex align-items-center gap-2 p-3 rounded border {{ $checked ? 'selected' : '' }}"
                                   style="cursor:pointer; transition: all .2s;"
                                   for="perm_{{ $permission->id }}">
                                <input type="checkbox"
                                       name="permissions[]"
                                       id="perm_{{ $permission->id }}"
                                       value="{{ $permission->id }}"
                                       class="form-check-input perm-checkbox mt-0"
                                       {{ $checked ? 'checked' : '' }}
                                       {{ $adminRole->name === 'super-admin' ? 'disabled' : '' }}>
                                <span class="small fw-medium">
                                    {{ ucwords(str_replace(['-', 'view'], [' ', ''], $permission->name)) }}
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($adminRole->name !== 'super-admin')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save me-1"></i> {{ __('Save Changes') }}
                    </button>
                    <a href="{{ route('admin-roles.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                </div>
                @else
                <div class="alert alert-info mb-0">
                    <i class="fa fa-info-circle me-1"></i>
                    {{ __('The super-admin role has all permissions and cannot be edited.') }}
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<style>
.permission-card { background: #f8fafc; border-color: #e2e8f0 !important; }
.permission-card:hover { background: #eef2ff; border-color: #818cf8 !important; }
.permission-card.selected { background: #eef2ff; border-color: #6366f1 !important; }
.permission-card input:checked ~ span { color: #4f46e5; font-weight: 600; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.permission-card');
    const checkboxes = document.querySelectorAll('.perm-checkbox');

    cards.forEach(card => {
        const cb = card.querySelector('input[type=checkbox]');
        if (cb) {
            cb.addEventListener('change', () => {
                card.classList.toggle('selected', cb.checked);
            });
        }
    });

    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', () => {
            checkboxes.forEach(cb => { if (!cb.disabled) { cb.checked = true; cb.closest('.permission-card').classList.add('selected'); }});
        });
    }
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', () => {
            checkboxes.forEach(cb => { if (!cb.disabled) { cb.checked = false; cb.closest('.permission-card').classList.remove('selected'); }});
        });
    }
});
</script>
@endsection
