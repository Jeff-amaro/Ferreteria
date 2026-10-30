@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between']) }}>
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if (! $slot->isEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
