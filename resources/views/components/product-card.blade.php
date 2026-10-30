@props(['product'])

<a href="{{ route('catalog.show', $product) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-lg">
    <div class="relative flex h-44 items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
        @else
            <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/80 text-primary-600 shadow-sm">
                <x-icon name="box" class="h-8 w-8" />
            </span>
        @endif
        <span class="absolute left-3 top-3 badge {{ $product->stock_color }}">{{ $product->stock_status }}</span>
    </div>
    <div class="flex flex-1 flex-col p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">{{ $product->category?->name ?? 'General' }}</p>
        <p class="mt-1 line-clamp-2 text-sm font-semibold text-slate-800 group-hover:text-primary-700">{{ $product->name }}</p>
        <p class="mt-1 text-xs text-slate-500">{{ $product->brand?->name ?? 'Sin marca' }} · {{ $product->sku }}</p>
        <div class="mt-3 flex items-end justify-between">
            <p class="text-lg font-bold text-primary-700">S/ {{ number_format((float) $product->price, 2) }}</p>
            <span class="text-xs text-slate-500">por {{ $product->unit }}</span>
        </div>
    </div>
</a>
