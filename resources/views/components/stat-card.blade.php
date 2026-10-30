@props(['label', 'value', 'hint' => null, 'icon' => 'chart', 'color' => 'bg-primary-50 text-primary-600'])

<div class="card p-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
            @endif
        </div>
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $color }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>
</div>
