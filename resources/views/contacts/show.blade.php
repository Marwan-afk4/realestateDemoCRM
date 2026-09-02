@extends('layouts.app')
@php $currentPage = 'contacts'; @endphp
@section('title', $contact->name)
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="pb-5">
        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-2">{{ $contact->name }}</h2>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @if($contact->source)
                        <span class="badge {{ $contact->source->badgeClass() }}">{{ $contact->source->label() }}</span>
                    @endif
                    @if($contact->preferred_area)
                        <span class="text-body-tertiary fs-9"><span class="uil uil-map-pin-alt me-1"></span>{{ $contact->preferred_area }}</span>
                    @endif
                    @if($contact->owner)
                        <span class="text-body-tertiary fs-9"><span class="uil uil-user me-1"></span>{{ $contact->owner->full_name }}</span>
                    @endif
                    @if($contact->last_contacted_at)
                        <span class="text-body-tertiary fs-9"><span class="uil uil-clock me-1"></span>{{ __('Last contacted') }} {{ $contact->last_contacted_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('contacts.index') }}" class="btn btn-phoenix-secondary">{{ __('Back') }}</a>
                    <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-phoenix-secondary"><span class="uil uil-edit me-1"></span>{{ __('Edit') }}</a>
                    @if($contact->tickets->contains(fn ($ticket) => $ticket->type === \App\Enums\PipelineTicketType::Lead && $ticket->stage->isOpen()))
                        <a href="{{ route('pipeline.index') }}" class="btn btn-phoenix-primary">{{ __('View pipeline') }}</a>
                    @else
                        <form method="POST" action="{{ route('contacts.pipeline', $contact) }}">
                            @csrf
                            <button class="btn btn-primary" type="submit">{{ __('Add to pipeline') }}</button>
                        </form>
                    @endif
                    @if($contact->telLink())
                        <a class="btn btn-primary" href="{{ $contact->telLink() }}"><span class="uil uil-phone me-1"></span>{{ __('Call') }}</a>
                    @endif
                    @if($contact->whatsappLink())
                        <a class="btn btn-success" target="_blank" href="{{ $contact->whatsappLink() }}"><span class="uil uil-whatsapp me-1"></span>{{ __('WhatsApp') }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-0 g-md-4 g-xl-6">
            <div class="col-md-5 col-lg-5 col-xl-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row align-items-center g-3 text-center text-xxl-start">
                            <div class="col-12 col-xxl-auto d-flex justify-content-center">
                                <x-crm-avatar :contact="$contact" size="5xl" />
                            </div>
                            <div class="col-12 col-xxl-auto flex-1">
                                <h3 class="fw-bolder mb-2">{{ $contact->name }}</h3>
                                <p class="mb-0 text-body-tertiary">{{ $contact->intent ? ucfirst($contact->intent) : __('Contact') }}</p>
                                @if($contact->uptownType)
                                    <p class="mb-0 fw-bold">{{ $contact->uptownType->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="mb-0">{{ __('About') }}</h3>
                            <a class="btn btn-link px-3" href="{{ route('contacts.edit', $contact) }}">{{ __('Edit') }}</a>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-1"><span class="me-2 uil uil-phone"></span><h5 class="text-body-highlight mb-0">{{ __('Phone') }}</h5></div>
                            @if($contact->telLink())
                                <a href="{{ $contact->telLink() }}">{{ $contact->phone }}</a>
                            @else
                                <p class="mb-0 text-body-secondary">—</p>
                            @endif
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-1"><span class="me-2 uil uil-whatsapp"></span><h5 class="text-body-highlight mb-0">{{ __('WhatsApp') }}</h5></div>
                            <p class="mb-0 text-body-secondary">{{ $contact->whatsapp ?? '—' }}</p>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-1"><span class="me-2 uil uil-envelope-alt"></span><h5 class="text-body-highlight mb-0">{{ __('Email') }}</h5></div>
                            @if($contact->email)
                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                            @else
                                <p class="mb-0 text-body-secondary">—</p>
                            @endif
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-1"><span class="me-2 uil uil-postcard"></span><h5 class="text-body-highlight mb-0">{{ __('National ID') }}</h5></div>
                            <p class="mb-0 text-body-secondary">{{ $contact->national_id ?? '—' }}</p>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-1"><span class="me-2 uil uil-dollar-alt"></span><h5 class="text-body-highlight mb-0">{{ __('Budget') }}</h5></div>
                            <p class="mb-0 text-body-secondary">
                                {{ ($contact->budget_min || $contact->budget_max) ? number_format((float) $contact->budget_min).' – '.number_format((float) $contact->budget_max) : '—' }}
                            </p>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-1"><span class="me-2 uil uil-tag-alt"></span><h5 class="text-body-highlight mb-0">{{ __('Tags') }}</h5></div>
                            <div>
                                @forelse ($contact->tagsList() as $tag)
                                    <span class="badge badge-phoenix badge-phoenix-secondary me-1">{{ $tag }}</span>
                                @empty
                                    <span class="text-body-secondary">—</span>
                                @endforelse
                            </div>
                        </div>
                        @if($contact->notes)
                            <div>
                                <div class="d-flex align-items-center mb-1"><span class="me-2 uil uil-file-alt"></span><h5 class="text-body-highlight mb-0">{{ __('Notes') }}</h5></div>
                                <p class="mb-0 text-body-secondary">{{ $contact->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header p-3 pb-0">
                        <ul class="nav nav-underline fs-9" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#crm-log" role="tab">{{ __('Log') }}</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#crm-message" role="tab">{{ __('Message') }}</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#crm-task" role="tab">{{ __('Task') }}</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="crm-log">
                                <form method="POST" action="{{ route('contacts.activities.store', $contact) }}">
                                    @csrf
                                    <x-form-select name="type" label="{{ __('Type') }}" :options="\App\Enums\ActivityType::labels()" required />
                                    <x-form-textarea name="body" label="{{ __('Note') }}" />
                                    <button class="btn btn-primary w-100" type="submit">{{ __('Save activity') }}</button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="crm-message">
                                <form method="POST" action="{{ route('contacts.message', $contact) }}" id="crm-message-form">
                                    @csrf
                                    <x-form-select name="channel" label="{{ __('Channel') }}" :options="\App\Enums\MessageChannel::labels()" required />
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="crm-template">
                                            <option value="">—</option>
                                            @foreach($templates as $template)
                                                <option value="{{ $template->id }}" data-channel="{{ $template->channel->value }}" data-subject="{{ e($template->subject) }}" data-body="{{ e($template->render(['name' => $contact->name, 'agent' => auth()->user()->full_name ?? ''])) }}">{{ $template->name }} ({{ $template->channel->label() }})</option>
                                            @endforeach
                                        </select>
                                        <label for="crm-template">{{ __('Template') }}</label>
                                    </div>
                                    <input type="hidden" name="template_id" id="crm-template-id">
                                    <x-form-input name="subject" type="text" label="{{ __('Subject') }}" />
                                    <x-form-textarea name="body" label="{{ __('Body') }}" required />
                                    <button class="btn btn-primary w-100" type="submit">{{ __('Send / open') }}</button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="crm-task">
                                <form method="POST" action="{{ route('crm-tasks.store') }}">
                                    @csrf
                                    <input type="hidden" name="contact_id" value="{{ $contact->id }}">
                                    <x-form-select name="type" label="{{ __('Type') }}" :options="\App\Enums\CrmTaskType::labels()" required />
                                    <x-form-input name="title" type="text" label="{{ __('Title') }}" required />
                                    <x-form-input name="due_at" type="datetime-local" label="{{ __('Due') }}" required />
                                    <button class="btn btn-primary w-100" type="submit">{{ __('Add task') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7 col-lg-7 col-xl-8">
                <nav class="navbar pb-4 px-0 sticky-top bg-body nav-underline-scrollspy">
                    <ul class="nav nav-underline fs-9">
                        <li class="nav-item"><a class="nav-link" href="#crm-pipeline">{{ __('Pipeline') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#crm-tasks">{{ __('Tasks') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#crm-related">{{ __('Related') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#crm-timeline">{{ __('Timeline') }}</a></li>
                    </ul>
                </nav>

                <div class="mb-6" id="crm-pipeline">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">{{ __('Pipeline') }}</h3>
                        <a href="{{ route('pipeline.index') }}" class="btn btn-link p-0">{{ __('Open board') }}</a>
                    </div>
                    @forelse ($contact->tickets as $ticket)
                        <div class="border-top border-translucent py-3">
                            <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    {!! $ticket->stage->badge() !!}
                                    <span class="badge {{ $ticket->type->badgeClass() }}">{{ $ticket->type->label() }}</span>
                                    @if($ticket->isLocked())
                                        <span class="badge badge-phoenix badge-phoenix-secondary">{{ __('Locked') }}</span>
                                    @endif
                                </div>
                                <div class="fs-9 text-body-tertiary">
                                    {{ $ticket->owner?->full_name ?? __('Unassigned') }}
                                    @if($ticket->openTask()?->due_at)
                                        · {{ __('Next') }} {{ $ticket->openTask()->due_at->format('M j, H:i') }}
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2">
                                @include('pipeline._assign-unit', ['ticket' => $ticket, 'inventoryUnits' => $inventoryUnits ?? []])
                                @if($ticket->inventory_unit_id)
                                    <a class="btn btn-sm btn-phoenix-secondary mt-2" href="{{ route('deals.create', ['pipeline_ticket_id' => $ticket->id]) }}">{{ __('Create deal for this unit') }}</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="crm-empty">
                            <p class="mb-3">{{ __('This contact is not on the pipeline yet.') }}</p>
                            <form method="POST" action="{{ route('contacts.pipeline', $contact) }}">
                                @csrf
                                <button class="btn btn-sm btn-primary" type="submit">{{ __('Add to pipeline') }}</button>
                            </form>
                        </div>
                    @endforelse
                </div>

                <div class="mb-6" id="crm-tasks">
                    <h3 class="mb-4">{{ __('Tasks') }}</h3>
                    @forelse ($tasks as $task)
                        <div class="row justify-content-between align-items-center py-3 gx-0 border-top border-translucent">
                            <div class="col">
                                <div class="d-flex align-items-center gap-3">
                                    <x-crm-avatar size="m" :initials="$task->isCompleted() ? '✓' : ($task->due_at?->format('j') ?? '•')" :color="$task->isOverdue() ? 'e63757' : ($task->isCompleted() ? '25b003' : '3874ff')" />
                                    <div>
                                        <div class="fw-semibold {{ $task->isCompleted() ? 'text-decoration-line-through text-body-tertiary' : '' }}">{{ $task->title }}</div>
                                        <div class="fs-9 text-body-tertiary">
                                            <span class="badge {{ $task->type->badgeClass() }}">{{ $task->type->label() }}</span>
                                            @if($task->due_at) · {{ $task->due_at->format('M j, Y H:i') }} @endif
                                            @if($task->isOverdue()) <span class="text-danger">{{ __('Overdue') }}</span> @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                @unless($task->isCompleted())
                                    <form method="POST" action="{{ route('crm-tasks.complete', $task) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-phoenix-success">{{ __('Done') }}</button>
                                    </form>
                                @else
                                    <span class="badge badge-phoenix badge-phoenix-success">{{ __('Done') }}</span>
                                @endunless
                            </div>
                        </div>
                    @empty
                        <div class="crm-empty">{{ __('No tasks.') }}</div>
                    @endforelse
                </div>

                <div class="mb-6" id="crm-related">
                    <h3 class="mb-4">{{ __('Related records') }}</h3>
                    @php
                        $related = collect()
                            ->merge($contact->leads->map(fn ($item) => ['label' => __('Lead'), 'href' => route('leads.show', $item), 'title' => '#'.$item->id.' '.$item->lead_name]))
                            ->merge($contact->deals->map(fn ($item) => ['label' => __('Deal'), 'href' => route('deals.show', $item), 'title' => '#'.$item->id.' '.$item->fullname]))
                            ->merge($contact->sellRequests->map(fn ($item) => ['label' => __('Sell request'), 'href' => route('sell-requests.show', $item), 'title' => '#'.$item->id]))
                            ->merge($contact->mortgageRequests->map(fn ($item) => ['label' => __('Mortgage'), 'href' => route('apartment-installments.show', $item), 'title' => '#'.$item->id]));
                    @endphp
                    @forelse ($related as $row)
                        <a href="{{ $row['href'] }}" class="d-flex justify-content-between align-items-center py-3 text-body text-decoration-none border-top border-translucent">
                            <span><span class="badge badge-phoenix badge-phoenix-secondary me-2">{{ $row['label'] }}</span>{{ $row['title'] }}</span>
                            <span class="uil uil-angle-right fs-7"></span>
                        </a>
                    @empty
                        <div class="crm-empty">{{ __('Nothing linked yet.') }}</div>
                    @endforelse
                </div>

                <div class="mb-4" id="crm-timeline">
                    <h3 class="mb-4">{{ __('Timeline') }}</h3>
                    <div class="crm-timeline">
                        @forelse ($activities as $activity)
                            <div class="crm-timeline-item">
                                <span class="crm-timeline-dot" style="background:#{{ $activity->type->color() }}">
                                    <span data-feather="{{ $activity->type->icon() }}"></span>
                                </span>
                                <div class="fw-semibold">{{ $activity->title }}</div>
                                <div class="fs-9 text-body-tertiary mb-1">{{ $activity->created_at->diffForHumans() }}@if($activity->user) · {{ $activity->user->full_name }} @endif</div>
                                @if($activity->body)
                                    <div class="text-body-secondary">{{ $activity->body }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="crm-empty">{{ __('No activity yet.') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('crm-template')?.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        document.getElementById('crm-template-id').value = option.value || '';
        if (!option.value) return;
        const channel = document.getElementById('channel');
        if (channel) channel.value = option.dataset.channel;
        const subject = document.getElementById('subject');
        if (subject) subject.value = option.dataset.subject || '';
        const body = document.getElementById('body');
        if (body) body.value = option.dataset.body || '';
        if (window.feather) feather.replace();
    });
    document.addEventListener('shown.bs.tab', function () {
        if (window.feather) feather.replace();
    });
</script>
@endpush
