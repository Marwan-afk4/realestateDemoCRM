<div class="{{ $divClass }} {{ $required ? 'required' : '' }}">
    <input 
        type="{{ $type }}" 
        name="{{ $name }}"
        value="{{ old(str_replace(['[', ']'], ['.', ''], $name), $value) }}"
        id="{{ $name }}"
        placeholder="{{ $label }}"
        class="{{ $class }} @error($name) is-invalid @enderror" 
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        @foreach($attrs ?? [] as $attribute => $v)
            @if(is_numeric($attribute))
                {{ $v }}
            @else
                {{ $attribute }}="{{ $v }}"
            @endif
        @endforeach
    >
    <label for="{{ $name }}">{{ $label }}</label>

    @error(str_replace(['[', ']'], ['.', ''], $name))
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
