@php
    $buyer = $deal->contact?->name ?? $deal->fullname;
    $broker = $deal->brocker?->user?->full_name ?? '—';
    $unitLabel = $unit?->address() ?? $unit?->code ?? '—';
    $price = $offer?->net_price ?? $deal->value;
@endphp
<div style="font-family: Tajawal, sans-serif; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <h2>{{ $type->label() }}</h2>
    <p>{{ __('Deal') }} #{{ $deal->id }} · {{ now()->toDateString() }}</p>
    <table style="width:100%; border-collapse: collapse" cellpadding="8">
        <tr><td><strong>{{ __('Buyer') }}</strong></td><td>{{ $buyer }}</td></tr>
        <tr><td><strong>{{ __('Phone') }}</strong></td><td>{{ $deal->contact?->phone ?? $deal->phone }}</td></tr>
        <tr><td><strong>{{ __('National ID') }}</strong></td><td>{{ $deal->contact?->national_id ?? $deal->nationality_id }}</td></tr>
        <tr><td><strong>{{ __('Broker') }}</strong></td><td>{{ $broker }}</td></tr>
        <tr><td><strong>{{ __('Unit') }}</strong></td><td>{{ $unitLabel }} ({{ $unit?->code }})</td></tr>
        <tr><td><strong>{{ __('Developer') }}</strong></td><td>{{ $deal->developer?->name ?? $unit?->developer?->name }}</td></tr>
        @if($offer)
            <tr><td><strong>{{ __('List price') }}</strong></td><td>{{ number_format((float) $offer->list_price) }}</td></tr>
            <tr><td><strong>{{ __('Discount') }}</strong></td><td>{{ number_format((float) $offer->discount) }}</td></tr>
            <tr><td><strong>{{ __('Net price') }}</strong></td><td>{{ number_format((float) $offer->net_price) }}</td></tr>
            <tr><td><strong>{{ __('Payment') }}</strong></td><td>{{ \App\Models\SaleOffer::paymentMethods()[$offer->payment_method] ?? $offer->payment_method }}</td></tr>
        @else
            <tr><td><strong>{{ __('Price') }}</strong></td><td>{{ $price ? number_format((float) $price) : '—' }}</td></tr>
        @endif
    </table>
    <p style="margin-top:2rem">
        @if($type === \App\Enums\SaleDocumentType::ReservationLetter)
            {{ __('This letter reserves the named unit for the buyer above. The hold expires if the sale contract is not signed in time.') }}
        @elseif($type === \App\Enums\SaleDocumentType::SaleContract)
            {{ __('This sale contract is for this unit, this buyer, and this broker. It is not the app terms of use.') }}
        @else
            {{ __('This offer is valid until the date shown on the deal. Accepting it issues a reservation letter and freezes the unit.') }}
        @endif
    </p>
</div>
