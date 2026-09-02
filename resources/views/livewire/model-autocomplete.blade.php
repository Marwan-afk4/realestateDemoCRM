<div class="position-relative">
    <div class="form-floating mb-3">
        <input type="text"
               wire:model.live.debounce.300ms="query"
               wire:blur="validateOnBlur"
               id="{{ $inputId }}"
               class="form-control @if($showError) is-invalid @endif"
               autocomplete="off"
        />
        <label for="{{ $inputId }}">{{ $label }}</label>
        @if ($showError)
            <div class="invalid-feedback">
                {{ __('Please select a valid option from the list.') }}
            </div>
        @endif
    </div>

    @if (!empty($results))
        <ul class="list-group position-absolute w-100 shadow" style="z-index: 1000; max-height: 200px; overflow-y: auto;">
            @foreach($results as $item)
                <li class="list-group-item list-group-item-action"
                    wire:click="select({{ $item['id'] }})"
                    style="cursor: pointer;">
                    {{ $item['name'] }}
                </li>
            @endforeach
        </ul>
    @endif

    <input type="hidden" name="{{ $hiddenInputName }}" value="{{ $selectedId }}">
</div>
