@extends('layouts.app')

@php
    $currentPage = 'payments';
    $tab = request('tab', 'all');
@endphp

@section('title', __('Payments'))

@section('content')
    <div class="container-fluid">
        <h1 class="mb-3">{{ __('Payments') }}</h1>

        {{-- Tabs --}}
        <div class="tabs-wrapper mb-4 border-b border-gray-200">
            <ul class="nav nav-pills mb-3 justify-content-center">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}"
                        href="{{ route('payments.index', ['tab' => 'pending']) }}">
                        <i class="fas fa-clock me-1"></i> {{ __('Pending') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'approved' ? 'active' : '' }}"
                        href="{{ route('payments.index', ['tab' => 'approved']) }}">
                        <i class="fas fa-check-circle me-1"></i> {{ __('History') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'rejected' ? 'active' : '' }}"
                        href="{{ route('payments.index', ['tab' => 'rejected']) }}">
                        <i class="fas fa-times-circle me-1"></i> {{ __('Rejected') }}
                    </a>
                </li>
            </ul>
        </div>


        {{-- Filters --}}
        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm me-1">
                <i class="fa fa-plus"></i> {{ __('Create Payment') }}
            </a>
        </div>

        {{-- Table --}}
        <div class='main-card mb-3 card'>
            <div class='card-body'>
                <table class="mb-0 table table-hover">
                    <tr>
                        <th> <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'receipt', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('receipt'))
                                    <i class="fas fa-filter text-danger"></i>
                                    @endif {{ __('Receipt') }} @if ($sortField === 'receipt')
                                        <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                    @endif
                            </a> </th>
                        <th> <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'payment_method_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('payment_method_id'))
                                    <i class="fas fa-filter text-danger"></i>
                                    @endif {{ __('Payment Method') }} @if ($sortField === 'payment_method_id')
                                        <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                    @endif
                            </a> </th> {{-- <th> <a href="{{ request()->fullUrlWithQuery(['sort' => 'receipt', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}"> @if (request()->filled('receipt'))<i class="fas fa-filter text-danger"></i>@endif {{ __("Receipt") }} @if ($sortField === 'receipt')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif </a> </th> --}} {{-- <th> <a href="{{ request()->fullUrlWithQuery(['sort' => 'user_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}"> @if (request()->filled('user_id'))<i class="fas fa-filter text-danger"></i>@endif {{ __("User") }} @if ($sortField === 'user_id')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif </a> </th> --}} <th> <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'brocker_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('brocker_id'))
                                    <i class="fas fa-filter text-danger"></i>
                                    @endif {{ __('Brocker') }} @if ($sortField === 'brocker_id')
                                        <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                    @endif
                            </a> </th>
                        <th> <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'plan_id', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('plan_id'))
                                    <i class="fas fa-filter text-danger"></i>
                                    @endif {{ __('Plan') }} @if ($sortField === 'plan_id')
                                        <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                    @endif
                            </a> </th>
                        <th> <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('status'))
                                    <i class="fas fa-filter text-danger"></i>
                                    @endif {{ __('Status') }} @if ($sortField === 'status')
                                        <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                    @endif
                            </a> </th>
                        <th> <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('created_at'))
                                    <i class="fas fa-filter text-danger"></i>
                                    @endif {{ __('Created At') }} @if ($sortField === 'created_at')
                                        <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                    @endif
                            </a> </th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                {{-- Receipt --}}
                                <td>
                                    @if ($payment->receipt)
                                        <img src="{{ $payment->receipt_url }}" alt="Receipt"
                                            style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                            data-bs-toggle="modal" data-bs-target="#imageModal"
                                            data-image-url="{{ $payment->receipt_url }}"
                                            data-image-title="Receipt #{{ $payment->id }}">
                                    @else
                                        <div
                                            style="width: 100px; height: 60px; background: #f8f9fa; border: 1px dashed #dee2e6;
											   display: flex; align-items: center; justify-content: center;
											   border-radius: 4px; color: #6c757d; font-size: 12px;">
                                            {{ __('No Receipt') }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Payment Method --}}
                                <td>
                                    @if ($payment->paymentmethod)
                                        <a href="{{ route('payment-methods.show', $payment->paymentmethod) }}">
                                            {{ $payment->paymentmethod?->method_name ?? '' }}
                                        </a>
                                    @endif
                                </td>

                                {{-- Brocker --}}
                                <td>
                                    @if ($payment->brocker)
                                        <a href="{{ route('brockers.show', $payment->brocker) }}">
                                            {{ $payment->brocker?->user?->full_name ?? '' }}
                                        </a>
                                    @endif
                                </td>

                                {{-- Plan --}}
                                <td>
                                    @if ($payment->plan)
                                        <a href="{{ route('plans.show', $payment->plan) }}">
                                            {{ $payment->plan?->name ?? '' }}
                                        </a>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td>{!! $payment->status->badge() !!}</td>

                                {{-- Created At --}}
                                <td>{{ $payment->created_at?->diffForHumans() ?? '-' }}</td>

                                {{-- Actions --}}
                                <td class="text-center">
                                    <a href="{{ route('payments.show', $payment) }}"
                                        class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fa fa-eye"></i> {{ __('Details') }}
                                    </a>
                                    @if ($tab === 'pending')
                                        <form action="{{ route('payments.approve', $payment->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('payments.reject', $payment->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">{{ __('No records found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $payments->links('pagination::custom') }}
            </div>
        </div>
    </div>


    {{-- Image Modal --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">{{ __('Receipt Image') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" src="" alt="" class="img-fluid"
                        style="max-height: 70vh; width: auto;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <a id="downloadLink" href="" download class="btn btn-primary">
                        <i class="fas fa-download"></i> {{ __('Download') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .tab-link {
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 500;
            color: #6b7280;
            /* gray-500 */
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .tab-link:hover {
            color: #374151;
            /* gray-700 */
        }

        .tab-link.active {
            color: #4f46e5;
            /* indigo-600 */
            border-bottom-color: #4f46e5;
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageModal = document.getElementById('imageModal');
            if (imageModal) {
                imageModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const imageUrl = button.getAttribute('data-image-url');
                    const imageTitle = button.getAttribute('data-image-title');

                    document.getElementById('modalImage').src = imageUrl;
                    document.getElementById('modalImage').alt = imageTitle;
                    document.getElementById('imageModalLabel').textContent = imageTitle ||
                        '{{ __('Receipt Image') }}';
                    document.getElementById('downloadLink').href = imageUrl;

                    const filename = imageTitle ?
                        imageTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.jpg' :
                        'receipt_image.jpg';
                    document.getElementById('downloadLink').setAttribute('download', filename);
                });
            }
        });
    </script>
@endpush
