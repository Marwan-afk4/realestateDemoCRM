@extends('layouts.app')
@php $currentPage = 'roles'; @endphp
@section('title', __('Create Role'))
@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">{{ __('Create Role') }}</h1>
        <a href="{{ route('admin-roles.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin-roles.store') }}">
                @csrf

                {{-- Role Name --}}
                <div class="mb-4">
                    <label for="name" class="form-label fw-semibold">{{ __('Role Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="{{ __('e.g. sales-manager') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <label class="permission-card d-flex align-items-center gap-2 p-3 rounded border cursor-pointer
                                          {{ old('permissions') && in_array($permission->id, old('permissions', [])) ? 'selected' : '' }}"
                                   style="cursor:pointer; transition: all .2s;"
                                   for="perm_{{ $permission->id }}">
                                <input type="checkbox"
                                       name="permissions[]"
                                       id="perm_{{ $permission->id }}"
                                       value="{{ $permission->id }}"
                                       class="form-check-input perm-checkbox mt-0"
                                       {{ old('permissions') && in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                <span class="small fw-medium">
                                    {{ ucwords(str_replace(['-', 'view'], [' ', ''], $permission->name)) }}
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('permissions')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> {{ __('Create Role') }}
                    </button>
                    <a href="{{ route('admin-roles.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                </div>
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
        cb.addEventListener('change', () => {
            card.classList.toggle('selected', cb.checked);
        });
    });

    document.getElementById('selectAll').addEventListener('click', () => {
        checkboxes.forEach(cb => { cb.checked = true; cb.closest('.permission-card').classList.add('selected'); });
    });

    document.getElementById('deselectAll').addEventListener('click', () => {
        checkboxes.forEach(cb => { cb.checked = false; cb.closest('.permission-card').classList.remove('selected'); });
    });
});
</script>
@endsection
