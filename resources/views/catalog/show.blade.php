@extends('layouts.public')

@section('title', $product->name)

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-primary-600">Inicio</a>
            <span class="mx-2">/</span>
            <a href="{{ route('catalog.index') }}" class="hover:text-primary-600">Catálogo</a>
            @if ($product->category)
                <span class="mx-2">/</span>
                <a href="{{ route('catalog.index', ['category' => $product->category_id]) }}" class="hover:text-primary-600">{{ $product->category->name }}</a>
            @endif
        </nav>

        <div class="mt-6 grid gap-8 lg:grid-cols-2">
            <div class="flex h-80 items-center justify-center overflow-hidden rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-100 to-slate-200 sm:h-96">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                @else
                    <span class="flex h-24 w-24 items-center justify-center rounded-3xl bg-white/80 text-primary-600 shadow-sm">
                        <x-icon name="box" class="h-12 w-12" />
                    </span>
                @endif
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-status-badge :color="$product->stock_color">{{ $product->stock_status }}</x-status-badge>
                    <span class="badge bg-slate-100 text-slate-600">{{ $product->category?->name ?? 'General' }}</span>
                    <span class="badge bg-slate-100 text-slate-600">{{ $product->brand?->name ?? 'Sin marca' }}</span>
                </div>

                <h1 class="mt-3 text-3xl font-bold text-slate-900">{{ $product->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">SKU {{ $product->sku }} · Proveedor: {{ $product->supplier?->business_name ?? 'No asignado' }}</p>

                <p class="mt-4 text-4xl font-bold text-primary-700">S/ {{ number_format((float) $product->price, 2) }}</p>
                <p class="text-sm text-slate-500">Precio por {{ $product->unit }} · IGV se calcula en la cotización</p>

                <div class="mt-6 rounded-2xl bg-slate-50 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Stock disponible</span>
                        <span class="font-bold text-slate-900">{{ $product->stock }} {{ $product->unit }}</span>
                    </div>
                    <div class="mt-1 flex items-center justify-between text-sm">
                        <span class="text-slate-600">Stock mínimo</span>
                        <span class="text-slate-700">{{ $product->min_stock }} {{ $product->unit }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Descripción</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $product->description ?? 'Sin descripción registrada.' }}</p>
                </div>

                @if ($product->specs)
                    <div class="mt-5">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">Especificaciones</h2>
                        <p class="mt-2 rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-700">{{ $product->specs }}</p>
                    </div>
                @endif

                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        @if (auth()->user()->hasRole(['administrador', 'vendedor']))
                            <a href="{{ route('pos.index') }}" class="btn-primary"><x-icon name="cart" class="h-4 w-4" /> Cotizar en punto de venta</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn-secondary">Ir al panel</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary">Iniciar sesión para cotizar</a>
                        <a href="{{ route('register') }}" class="btn-secondary">Crear cuenta</a>
                    @endauth
                    <a href="{{ route('catalog.index') }}" class="btn-ghost">Seguir explorando</a>
                </div>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-14">
                <div class="flex items-end justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">Productos relacionados</h2>
                    <a href="{{ route('catalog.index', ['category' => $product->category_id]) }}" class="text-sm font-semibold text-primary-600 hover:underline">Ver categoría</a>
                </div>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($related as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
