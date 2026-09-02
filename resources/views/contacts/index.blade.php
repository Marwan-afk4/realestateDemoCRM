@extends('layouts.app')
@php $currentPage = 'contacts'; @endphp
@section('title', __('Contacts'))
@section('content')
@include('crm.styles')
<div class="container-fluid">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h2 class="mb-2">{{ __('Contacts') }}</h2>
                <h5 class="text-body-tertiary fw-semibold">{{ __('One person record for every lead, deal, and request.') }}</h5>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <form method="GET">
                    <div class="search-box">
                        <input type="search" name="keyword" class="form-control search-input" placeholder="{{ __('Search contacts') }}" value="{{ request('keyword') }}">
                        <span class="fas fa-search search-box-icon"></span>
                    </div>
                </form>
                <a href="{{ route('contacts.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-2"></span>{{ __('Create Contact') }}
                </a>
            </div>
        </div>
    </div>

    <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
        <div class="table-responsive scrollbar mx-n1 px-1">
            <table class="table table-sm fs-9 mb-0 crm-table">
                <thead>
                    <tr>
                        <th class="white-space-nowrap align-middle ps-0" style="width:30%;">{{ __('Name') }}</th>
                        <th class="align-middle white-space-nowrap">{{ __('Phone') }}</th>
                        <th class="align-middle white-space-nowrap">{{ __('Email') }}</th>
                        <th class="align-middle white-space-nowrap">{{ __('Source') }}</th>
                        <th class="align-middle white-space-nowrap">{{ __('Owner') }}</th>
                        <th class="align-middle text-center">{{ __('Tickets') }}</th>
                        <th class="align-middle text-center">{{ __('Deals') }}</th>
                        <th class="align-middle text-end pe-0">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @forelse ($contacts as $contact)
                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                            <td class="align-middle white-space-nowrap ps-0">
                                <div class="d-flex align-items-center">
                                    <x-crm-avatar :contact="$contact" size="xl" class="me-3" />
                                    <div>
                                        <a class="fs-8 fw-bold" href="{{ route('contacts.show', $contact) }}">{{ $contact->name }}</a>
                                        @if($contact->tagsList())
                                            <div class="fs-10 text-body-tertiary">{{ implode(' · ', $contact->tagsList()) }}</div>
                                        @elseif($contact->preferred_area)
                                            <div class="fs-10 text-body-tertiary">{{ $contact->preferred_area }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle white-space-nowrap">
                                @if($contact->telLink())
                                    <a class="fw-semibold text-body-highlight" href="{{ $contact->telLink() }}">{{ $contact->phone }}</a>
                                @else
                                    <span class="text-body-tertiary">—</span>
                                @endif
                            </td>
                            <td class="align-middle white-space-nowrap">
                                @if($contact->email)
                                    <a class="fw-semibold text-body-highlight" href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                @else
                                    <span class="text-body-tertiary">—</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge {{ $contact->source?->badgeClass() ?? 'badge-phoenix badge-phoenix-secondary' }}">{{ $contact->source?->label() ?? '—' }}</span>
                            </td>
                            <td class="align-middle fw-semibold text-body-highlight">{{ $contact->owner?->full_name ?? '—' }}</td>
                            <td class="align-middle text-center fw-semibold">{{ $contact->tickets_count }}</td>
                            <td class="align-middle text-center fw-semibold">{{ $contact->deals_count }}</td>
                            <td class="align-middle white-space-nowrap text-end pe-0">
                                <div class="btn-reveal-trigger position-static">
                                    <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="fas fa-ellipsis-h fs-10"></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end py-2">
                                        <a class="dropdown-item" href="{{ route('contacts.show', $contact) }}">{{ __('View') }}</a>
                                        <a class="dropdown-item" href="{{ route('contacts.edit', $contact) }}">{{ __('Edit') }}</a>
                                        @if($contact->tickets_count === 0)
                                            <form method="POST" action="{{ route('contacts.pipeline', $contact) }}">
                                                @csrf
                                                <button class="dropdown-item" type="submit">{{ __('Add to pipeline') }}</button>
                                            </form>
                                        @endif
                                        @if($contact->telLink())
                                            <a class="dropdown-item" href="{{ $contact->telLink() }}">{{ __('Call') }}</a>
                                        @endif
                                        @if($contact->whatsappLink())
                                            <a class="dropdown-item" target="_blank" href="{{ $contact->whatsappLink() }}">{{ __('WhatsApp') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6">
                                <div class="crm-empty mx-auto" style="max-width: 24rem;">
                                    <span class="uil uil-users-alt fs-3 d-block mb-2"></span>
                                    {{ __('No contacts yet.') }}
                                    <div class="mt-3">
                                        <a href="{{ route('contacts.create') }}" class="btn btn-sm btn-primary">{{ __('Create Contact') }}</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contacts->hasPages())
            <div class="py-3">{{ $contacts->links() }}</div>
        @endif
    </div>
</div>
@endsection
