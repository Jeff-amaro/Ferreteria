@props(['name', 'label' => null, 'options' => [], 'selected' => null, 'placeholder' => null, 'required' => false, 'help' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }} @if ($required)<span class="text-rose-500">*</span>@endif</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'input']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $selected) === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($help)
        <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</div>
