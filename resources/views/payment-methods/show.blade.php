@extends('layouts.app')
@php
	$currentPage = 'payment-methods';
@endphp
@section('title', $paymentMethod->name)
@section('content')
<div class="container-fluid">
	<h1>{{ $paymentMethod->method_name }}</h1>
	<div class="mb-3">
		<a href="{{ route('payment-methods.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Payment Methods')}}</a>
		<a href='{{ route('payment-methods.edit', $paymentMethod) }}' class="btn btn-warning btn-sm me-1">{{ __('Edit') }} <i class="fa fa-edit"></i></a>
		{{-- <a href="{{ route('payment-methods.payments', $paymentMethod) }}" class="list-group-item">{{ __('payment') }}</a> --}}
	</div>
	<div class="card">
		<div class="card-body">
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<strong>{{ __("Id") }}:</strong> {{ $paymentMethod->id }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Method Name") }}:</strong> {{ $paymentMethod->method_name }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Image") }}:</strong>
                    @if ($paymentMethod->image)
                            <div class="mt-2">
                                <img src="{{ $paymentMethod->image_url }}" alt="{{ $paymentMethod->method_name }}"
                                    style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                    onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div
                                    style="display: none; padding: 20px; background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 8px; color: #6c757d; text-align: center;">
                                    <i class="fas fa-image"></i><br>
                                    {{ __('Image could not be loaded') }}<br>
                                    <small>{{ $paymentMethod->image }}</small>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">{{ __('No image uploaded') }}</span>
                        @endif
				</li>
				<li class="list-group-item">
					<strong>{{ __("Status") }}:</strong> {!! $paymentMethod->status->badge() !!}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Created At") }}:</strong> {{ $paymentMethod->created_at?->diffForHumans() ?? '-' }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Updated At") }}:</strong> {{ $paymentMethod->updated_at?->diffForHumans() ?? '-' }}
				</li>
			</ul>
		</div>
	</div>
	<div class="mt-3">
		{{-- <form method='POST' action='{{ route('payment-methods.destroy', $paymentMethod) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
			<input type='hidden' name='_method' value='DELETE'>
			<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
		</form> --}}
	</div>
</div>
@endsection
