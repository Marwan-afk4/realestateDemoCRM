@extends('layouts.app')
@php
	$currentPage = 'payments';
@endphp
@section('title', $payment->name)
@section('content')
<div class="container-fluid">
	<h1>{{ $payment->id }}</h1>
	<div class="mb-3">
		<a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm me-1"> <i class="fa fa-arrow-left"></i> {{__('Back to')}} {{__('Payments')}}</a>
		{{-- <a href='{{ route('payments.edit', $payment) }}' class="btn btn-warning btn-sm me-1">{{ __('Edit') }} <i class="fa fa-edit"></i></a> --}}
	</div>
	<div class="card">
		<div class="card-body">
			<ul class="list-group list-group-flush">
				<li class="list-group-item">
					<strong>{{ __("Id") }}:</strong> {{ $payment->id }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Payment Method") }}:</strong>
                        @if ($payment->paymentmethod)
							<a href='{{ route('payment-methods.show', $payment->paymentmethod) }}'>
								{{$payment->paymentmethod?->method_name ?? '' }}
							</a>
						@endif
				</li>
				<li class="list-group-item">
					<strong>{{ __("Receipt") }}:</strong>
                    @if ($payment->receipt)
                            <div class="mt-2">
                                <img src="{{ $payment->receipt_url }}" alt="{{ $payment->method_name }}"
                                    style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                                    onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div
                                    style="display: none; padding: 20px; background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 8px; color: #6c757d; text-align: center;">
                                    <i class="fas fa-image"></i><br>
                                    {{ __('Image could not be loaded') }}<br>
                                    <small>{{ $payment->receipt }}</small>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">{{ __('No image uploaded') }}</span>
                        @endif
				</li>
				<li class="list-group-item">
					<strong>{{ __("Brocker") }}:</strong>
                        @if ($payment->brocker)
                            <a href='{{ route('brockers.show', $payment->brocker) }}'>
                                {{$payment->brocker?->user->full_name ?? '' }}
                            </a>
                        @endif
				</li>
				<li class="list-group-item">
					<strong>{{ __("Plan") }}:</strong>
                        @if ($payment->plan)
							<a href='{{ route('plans.show', $payment->plan) }}'>
								{{$payment->plan?->name ?? '' }}
							</a>
						@endif
				</li>
				<li class="list-group-item">
					<strong>{{ __("Status") }}:</strong> {!! $payment->status->badge() !!}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Created At") }}:</strong> {{ $payment->created_at?->diffForHumans() ?? '-' }}
				</li>
				<li class="list-group-item">
					<strong>{{ __("Updated At") }}:</strong> {{ $payment->updated_at?->diffForHumans() ?? '-' }}
				</li>
			</ul>
		</div>
	</div>
	<div class="mt-3">
		{{-- <form method='POST' action='{{ route('payments.destroy', $payment) }}' onsubmit='return confirm("Are you sure you want to delete this item?")'>
			<input type='hidden' name='_method' value='DELETE'>
			<button type='submit' class="btn btn-square btn-danger">{{ __('Delete') }}</button>
		</form> --}}
	</div>
</div>
@endsection
