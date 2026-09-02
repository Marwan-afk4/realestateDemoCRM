@extends('layouts.app')
@php
    $currentPage = 'payment-methods';
@endphp
@section('title', __('Payment Methods'))
@section('content')
    <div class="container-fluid">
        <h1 class="mb-3">{{ __('Payment Methods') }}</h1>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('payment-methods.create') }}"
                class="btn btn-primary btn-sm me-1">{{ __('Create Payment Method') }} <i class="fa fa-plus"></i></a>
            {{-- <div class="search-wrapper">
                <form action="{{ route(Route::currentRouteName(), [], false) }}" method="GET">
                    <div class="input-group">
                        @if (request()->query())
                            <a class="btn btn-secondary" href="{{ route(Route::currentRouteName(), [], false) }}">
                                <i class="fa fa-times"></i>
                            </a>
                        @endif

                        <input type="text" name="keyword" class="form-control" placeholder="{{ __('Keyword...') }}"
                            value="{{ request('keyword') }}">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div> --}}
        </div>
        <div class='main-card mb-3 card'>
            <div class='card-body'>
                <table class="mb-0 table table-hover">
                    <tr>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'image', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('image'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Image') }}
                                @if ($sortField === 'image')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'method_name', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('method_name'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Method Name') }}
                                @if ($sortField === 'method_name')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        {{-- <th>
						<a href="{{ request()->fullUrlWithQuery(['sort' => 'image', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
							@if (request()->filled('image'))<i class="fas fa-filter text-danger"></i>@endif
							{{ __("Image") }}
							@if ($sortField === 'image')<i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>@endif
						</a>
					</th> --}}
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('status'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Status') }}
                                @if ($sortField === 'status')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a
                                href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}">
                                @if (request()->filled('created_at'))
                                    <i class="fas fa-filter text-danger"></i>
                                @endif
                                {{ __('Created At') }}
                                @if ($sortField === 'created_at')
                                    <i class="text-primary">{{ $sortOrder === 'asc' ? '▼' : '▲' }}</i>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                    @foreach ($paymentMethods as $paymentMethod)
                        <tr>
                            <td>
                                @if ($paymentMethod->image)
                                    <img src="{{ $paymentMethod->image_url }}" alt="{{ $paymentMethod->method_name }}"
                                        style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                        data-bs-toggle="modal" data-bs-target="#imageModal"
                                        data-image-url="{{ $paymentMethod->image_url }}"
                                        data-image-title="{{ $paymentMethod->method_name }}">
                                @else
                                    <div
                                        style="width: 100px; height: 60px; background: #f8f9fa; border: 1px dashed #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 4px; color: #6c757d; font-size: 12px;">
                                        No Image
                                    </div>
                                @endif
                            </td>
                            <td>{{ $paymentMethod->method_name }}</td>
                            <td>{!! $paymentMethod->status->badge() !!}</td>
                            <td>{{ $paymentMethod->created_at?->diffForHumans() ?? '-' }}</td>
                            <td class="text-center">
                                <a href='{{ route('payment-methods.show', $paymentMethod) }}'
                                    class="btn btn-subtle-primary btn-sm me-1">{{ __('Details') }} <i
                                        class="fa fa-eye"></i></a>
                                <form action="{{ route('payment-methods.destroy', $paymentMethod) }}" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('{{ __('Are you sure you want to delete this payment method?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-subtle-danger btn-sm">
                                        {{ __('Delete') }} <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
                {{ $paymentMethods->links('pagination::custom') }}
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">{{ __('Payment Method Image') }}</h5>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageModal = document.getElementById('imageModal');

            if (imageModal) {
                imageModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const imageUrl = button.getAttribute('data-image-url');
                    const imageTitle = button.getAttribute('data-image-title');

                    // Update modal content
                    document.getElementById('modalImage').src = imageUrl;
                    document.getElementById('modalImage').alt = imageTitle;
                    document.getElementById('imageModalLabel').textContent = imageTitle ||
                        '{{ __('Payment Method Image') }}';
                    document.getElementById('downloadLink').href = imageUrl;

                    // Set download filename
                    const filename = imageTitle ? imageTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase() +
                        '.jpg' : 'payment_method_image.jpg';
                    document.getElementById('downloadLink').setAttribute('download', filename);
                });
            }
        });
    </script>
@endpush
