@extends('layouts.app')
@php $currentPage = 'admins'; @endphp
@section('title', __('Admins'))
@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0">{{ __('Admins') }}</h1>
        <a href="{{ route('admin-users.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus me-1"></i> {{ __('Add Admin') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search --}}
    <div class="mb-3">
        <form action="{{ route('admin-users.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="keyword" class="form-control form-control-sm w-auto"
                   placeholder="{{ __('Search...') }}" value="{{ request('keyword') }}">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
            @if(request('keyword'))
                <a href="{{ route('admin-users.index') }}" class="btn btn-secondary btn-sm"><i class="fa fa-times"></i></a>
            @endif
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Created') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle" style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.8rem;flex-shrink:0;">
                                    {{ strtoupper(substr($admin->first_name,0,1)) }}
                                </div>
                                <span>{{ $admin->full_name }}</span>
                            </div>
                        </td>
                        <td><a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a></td>
                        <td>{{ $admin->phone }}</td>
                        <td>
                            @if($admin->roles->first())
                                <span class="badge rounded-pill" style="background:#eef2ff;color:#4f46e5;">
                                    <i class="fa fa-shield-alt me-1"></i>{{ $admin->roles->first()->name }}
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ __('No role') }}</span>
                            @endif
                        </td>
                        <td>{!! $admin->status->badge() !!}</td>
                        <td>{{ $admin->created_at?->diffForHumans() ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin-users.edit', $admin) }}" class="btn btn-subtle-warning btn-sm me-1">
                                <i class="fa fa-edit"></i>
                            </a>
                            @if(auth()->id() !== $admin->id)
                            <form action="{{ route('admin-users.destroy', $admin) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('Delete this admin?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-subtle-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fa fa-user-shield fa-2x mb-2 d-block"></i>
                            {{ __('No admins found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($admins->hasPages())
        <div class="card-footer">
            {{ $admins->links('pagination::custom') }}
        </div>
        @endif
    </div>
</div>
@endsection
