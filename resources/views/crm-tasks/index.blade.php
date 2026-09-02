@extends('layouts.app')
@php $currentPage = 'crm-tasks'; @endphp
@section('title', __('Tasks'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="mb-2">{{ __('Tasks') }}</h2>
            <p class="text-body-tertiary mb-0">{{ __('Follow-ups, visits, and assignment expiry — not a date column.') }}</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('crm-tasks.index') }}" class="btn btn-sm {{ request('filter') ? 'btn-phoenix-secondary' : 'btn-primary' }}">{{ __('All') }}</a>
            <a href="{{ route('crm-tasks.index', ['filter' => 'open']) }}" class="btn btn-sm {{ request('filter') === 'open' ? 'btn-primary' : 'btn-phoenix-secondary' }}">{{ __('Open') }}</a>
            <a href="{{ route('crm-tasks.index', ['filter' => 'today']) }}" class="btn btn-sm {{ request('filter') === 'today' ? 'btn-primary' : 'btn-phoenix-secondary' }}">{{ __('Today') }}</a>
            <a href="{{ route('crm-tasks.index', ['filter' => 'overdue']) }}" class="btn btn-sm {{ request('filter') === 'overdue' ? 'btn-danger' : 'btn-phoenix-secondary' }}">{{ __('Overdue') }}</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover crm-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">{{ __('Due') }}</th>
                                    <th>{{ __('Task') }}</th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Owner') }}</th>
                                    <th class="pe-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tasks as $task)
                                    <tr>
                                        <td class="ps-4 text-nowrap">
                                            <span class="fw-semibold {{ $task->isOverdue() ? 'text-danger' : '' }}">{{ $task->due_at?->format('M j, H:i') ?? '—' }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $task->title }}</div>
                                            <span class="badge {{ $task->type->badgeClass() }}">{{ $task->type->label() }}</span>
                                            @if($task->isOverdue())
                                                <span class="badge badge-phoenix badge-phoenix-danger">{{ __('Overdue') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->contact)
                                                <a href="{{ route('contacts.show', $task->contact) }}" class="fw-semibold">{{ $task->contact->name }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $task->owner?->full_name ?? '—' }}</td>
                                        <td class="pe-4 text-end">
                                            @unless($task->isCompleted())
                                                <form method="POST" action="{{ route('crm-tasks.complete', $task) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-phoenix-success">{{ __('Done') }}</button>
                                                </form>
                                            @else
                                                <span class="badge badge-phoenix badge-phoenix-success">{{ __('Done') }}</span>
                                            @endunless
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6"><div class="crm-empty">{{ __('No tasks.') }}</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($tasks->hasPages())
                    <div class="card-footer">{{ $tasks->links() }}</div>
                @endif
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('Upcoming') }}</h5>
                    @forelse ($calendarTasks->sortBy('due_at') as $task)
                        <a href="{{ route('contacts.show', $task->contact_id) }}" class="d-flex gap-3 py-3 text-decoration-none text-body {{ !$loop->last ? 'border-bottom border-translucent' : '' }}">
                            <x-crm-avatar size="l" :initials="$task->due_at?->format('j') ?? '•'" color="3874ff" />
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate">{{ $task->contact?->name }}</div>
                                <div class="fs-9 text-body-tertiary text-truncate">{{ $task->title }} · {{ $task->due_at?->format('M j, H:i') }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="crm-empty">{{ __('Nothing scheduled.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
