@php $contact = $contact ?? null; @endphp
<div class="row g-4">
    <div class="col-lg-6">
        <h5 class="mb-3">{{ __('Person') }}</h5>
        <x-form-input name="name" type="text" label="{{ __('Name') }}" :value="old('name', $contact?->name)" required />
        <x-form-select name="source" label="{{ __('Source') }}" :options="$sources" :selected="old('source', $contact?->source?->value)" required />
        <x-form-input name="phone" type="text" label="{{ __('Phone') }}" :value="old('phone', $contact?->phone)" required />
        <x-form-input name="whatsapp" type="text" label="{{ __('WhatsApp') }}" :value="old('whatsapp', $contact?->whatsapp)" />
        <x-form-input name="phone_secondary" type="text" label="{{ __('Secondary phone') }}" :value="old('phone_secondary', $contact?->phone_secondary)" />
        <x-form-input name="email" type="email" label="{{ __('Email') }}" :value="old('email', $contact?->email)" />
        <x-form-input name="national_id" type="text" label="{{ __('National ID') }}" :value="old('national_id', $contact?->national_id)" />
    </div>
    <div class="col-lg-6">
        <h5 class="mb-3">{{ __('Preferences') }}</h5>
        <x-form-input name="preferred_area" type="text" label="{{ __('Preferred area') }}" :value="old('preferred_area', $contact?->preferred_area)" />
        <div class="row">
            <div class="col-md-6">
                <x-form-input name="budget_min" type="number" label="{{ __('Budget min') }}" :value="old('budget_min', $contact?->budget_min)" />
            </div>
            <div class="col-md-6">
                <x-form-input name="budget_max" type="number" label="{{ __('Budget max') }}" :value="old('budget_max', $contact?->budget_max)" />
            </div>
        </div>
        <x-form-select name="uptown_type_id" label="{{ __('Unit type') }}" :options="$uptownTypes" :selected="old('uptown_type_id', $contact?->uptown_type_id)" />
        <x-form-select name="intent" label="{{ __('Intent') }}" :options="['buy' => __('Buy'), 'rent' => __('Rent'), 'sell' => __('Sell'), 'mortgage' => __('Mortgage')]" :selected="old('intent', $contact?->intent)" />
        <x-form-select name="payment_preference" label="{{ __('Payment') }}" :options="['cash' => __('Cash'), 'installment' => __('Installment')]" :selected="old('payment_preference', $contact?->payment_preference)" />
        <x-form-input name="tags" type="text" label="{{ __('Tags (comma separated)') }}" :value="old('tags', $contact ? implode(', ', $contact->tagsList()) : '')" />
        <x-form-textarea name="notes" label="{{ __('Notes') }}" :value="old('notes', $contact?->notes)" />
    </div>
</div>
