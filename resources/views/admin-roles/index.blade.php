@extends('layouts.app')
@php $currentPage = 'roles'; @endphp
@section('title', __('Roles'))
@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">{{ __('Roles & Permissions') }}</h1>
        <a href="{{ route('admin-roles.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus me-1"></i> {{ __('Create Role') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        @forelse($roles as $role)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex align-items-center justify-content-between py-3"
                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-purple fw-bold" style="color:#764ba2;">
                            <i class="fa fa-shield-alt me-1"></i>
                            {{ ucfirst($role->name) }}
                        </span>
                        <span class="badge bg-white bg-opacity-25 text-white">
                            {{ $role->permissions->count() }} {{ __('permissions') }}
                        </span>
                    </div>
                    @if($role->name !== 'super-admin')
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin-roles.edit', $role) }}" class="btn btn-sm btn-light py-0 px-2">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('admin-roles.destroy', $role) }}" method="POST"
                              onsubmit="return confirm('{{ __('Delete this role?') }}')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger py-0 px-2" type="submit">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <span class="badge bg-warning text-dark"><i class="fa fa-lock me-1"></i>{{ __('Protected') }}</span>
                    @endif
                </div>
                <div class="card-body p-3">
                    @if($role->permissions->count())
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($role->permissions->sortBy('name') as $perm)
                                <span class="badge rounded-pill" style="background:#eef2ff;color:#4f46e5;font-size:0.7rem;">
                                    {{ str_replace('view-', '', $perm->name) }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">{{ __('No permissions assigned.') }}</p>
                    @endif
                </div>
                @if($role->name !== 'super-admin')
                <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                    <a href="{{ route('admin-roles.edit', $role) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fa fa-sliders-h me-1"></i> {{ __('Edit Permissions') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card text-center py-5 border-0 shadow-sm">
                <div class="card-body">
                    <i class="fa fa-shield-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('No roles found. Create your first role.') }}</p>
                    <a href="{{ route('admin-roles.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus me-1"></i> {{ __('Create Role') }}
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
