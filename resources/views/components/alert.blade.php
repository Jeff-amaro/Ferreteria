@props(['type' => 'success', 'dismissible' => true])

@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-rose-200 bg-rose-50 text-rose-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-sky-200 bg-sky-50 text-sky-800',
    ];
    $icons = ['success' => 'check', 'error' => 'alert', 'warning' => 'alert', 'info' => 'alert'];
@endphp

<div data-flash {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm '.($styles[$type] ?? $styles['info'])]) }}>
    <x-icon :name="$icons[$type] ?? 'alert'" class="mt-0.5 h-5 w-5 shrink-0" />
    <div class="flex-1">{{ $slot }}</div>
    @if ($dismissible)
        <button type="button" data-dismiss class="rounded-md p-0.5 opacity-60 transition hover:opacity-100" aria-label="Cerrar">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    @endif
</div>
