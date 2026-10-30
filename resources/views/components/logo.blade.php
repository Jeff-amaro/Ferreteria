@props(['compact' => false])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-base font-bold tracking-tight text-white shadow-lg shadow-primary-600/20">
        EJJ
    </span>
    @unless($compact)
        <span class="leading-tight">
            <span class="block text-base font-bold text-slate-900">Ferretería EJJ</span>
            <span class="block text-xs font-medium text-slate-500">Ventas e inventario</span>
        </span>
    @endunless
</div>
