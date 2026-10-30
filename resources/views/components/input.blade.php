@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false, 'help' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }} @if ($required)<span class="text-rose-500">*</span>@endif</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'input']) }}
    >
    @if ($help)
        <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</div>
