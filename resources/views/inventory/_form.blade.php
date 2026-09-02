@php $unit = $unit ?? null; @endphp
<div class="row">
    <div class="col-md-6">
        <x-form-select name="compound_id" label="{{ __('Compound / project') }}" :options="$compounds" :selected="old('compound_id', $unit?->compound_id)" required />
    </div>
    <div class="col-md-6">
        <x-form-select name="developer_id" label="{{ __('Developer') }}" :options="$developers" :selected="old('developer_id', $unit?->developer_id)" />
    </div>
    <div class="col-md-6">
        <x-form-select name="uptown_id" label="{{ __('Listing card (optional)') }}" :options="$listings" :selected="old('uptown_id', $unit?->uptown_id)" />
    </div>
    <div class="col-md-6">
        <x-form-input name="phase" type="text" label="{{ __('Phase') }}" :value="old('phase', $unit?->phase)" />
    </div>
    <div class="col-md-4">
        <x-form-input name="building" type="text" label="{{ __('Building') }}" :value="old('building', $unit?->building)" />
    </div>
    <div class="col-md-4">
        <x-form-input name="floor" type="text" label="{{ __('Floor') }}" :value="old('floor', $unit?->floor)" />
    </div>
    <div class="col-md-4">
        <x-form-input name="unit_number" type="text" label="{{ __('Unit number') }}" :value="old('unit_number', $unit?->unit_number)" required />
    </div>
    <div class="col-md-6">
        <x-form-input name="list_price" type="number" label="{{ __('List price') }}" :value="old('list_price', $unit?->list_price)" />
    </div>
    <div class="col-md-6">
        <x-form-input name="current_price" type="number" label="{{ __('Current price') }}" :value="old('current_price', $unit?->current_price)" />
    </div>
</div>
