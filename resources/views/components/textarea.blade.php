@props(['name', 'label' => null, 'value' => null, 'rows' => 4, 'required' => false, 'help' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }} @if ($required)<span class="text-rose-500">*</span>@endif</label>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'input']) }}
    >{{ old($name, $value) }}</textarea>
    @if ($help)
        <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</div>
