@php $unit = $ticket->inventoryUnit; @endphp
@if($unit)
    <p class="mb-2 fs-9">
        <span class="uil uil-building me-1"></span>
        <a href="{{ route('inventory-units.show', $unit) }}">{{ $unit->code }}</a>
        <span class="badge badge-phoenix {{ $unit->status->phoenixBadge() }} fs-10">{{ $unit->status->label() }}</span>
    </p>
@endif
<details class="mt-2">
    <summary class="fs-10 fw-bold text-body-tertiary" style="cursor:pointer;list-style:none;">{{ __('Assign unit') }}</summary>
    <form method="POST" action="{{ route('pipeline.unit', $ticket) }}" class="mt-2">
        @csrf
        <select name="inventory_unit_id" class="form-select form-select-sm mb-1">
            <option value="">{{ __('No unit') }}</option>
            @foreach($inventoryUnits as $id => $label)
                <option value="{{ $id }}" @selected($ticket->inventory_unit_id == $id)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-phoenix-primary w-100">{{ __('Save unit') }}</button>
    </form>
</details>
