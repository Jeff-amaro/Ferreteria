@extends('layouts.public')

@section('title', 'Inicio')

@section('content')
    <section class="relative overflow-hidden bg-primary-600 text-white">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-white/5"></div>

        <div class="relative mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-24">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-white/60">Ferretería EJJ</p>
                <h1 class="mt-4 text-4xl font-bold leading-tight sm:text-5xl">Todo para tu obra, con stock real y precios claros.</h1>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-white/80">
                    Consulta nuestro catálogo de herramientas, materiales, plomería, electricidad y seguridad industrial. Cotiza en línea y recibe atención personalizada.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-primary-700 shadow-sm transition hover:bg-primary-50">
                        <x-icon name="search" class="h-4 w-4" /> Explorar catálogo
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Ir al panel <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Crear cuenta <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    @endauth
                </div>
                <div class="mt-10 grid max-w-lg grid-cols-3 gap-4 text-sm">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                        <p class="text-2xl font-bold">{{ $categories->count() }}</p>
                        <p class="text-white/70">Categorías</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                        <p class="text-2xl font-bold">{{ $categories->sum('products_count') }}</p>
                        <p class="text-white/70">Productos</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                        <p class="text-2xl font-bold">18%</p>
                        <p class="text-white/70">IGV incluido</p>
                    </div>
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="rounded-3xl border border-white/15 bg-white/10 p-6 backdrop-blur">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($featured->take(4) as $product)
                            <div class="rounded-2xl bg-white p-4 text-slate-800 shadow-sm">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-600"><x-icon name="box" class="h-5 w-5" /></span>
                                <p class="mt-3 line-clamp-2 text-sm font-semibold">{{ $product->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $product->category?->name }}</p>
                                <p class="mt-2 text-lg font-bold text-primary-700">S/ {{ number_format((float) $product->price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-primary-600">Catálogo</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">Compra por categoría</h2>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Ver todos los productos →</a>
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('catalog.index', ['category' => $category->id]) }}" class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-primary-300 hover:shadow-md">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-600"><x-icon name="tag" class="h-5 w-5" /></span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-semibold text-slate-800 group-hover:text-primary-700">{{ $category->name }}</span>
                        <span class="block text-xs text-slate-500">{{ $category->products_count }} productos</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="bg-slate-50 py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-primary-600">Destacados</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">Productos con stock disponible</h2>
                </div>
                <a href="{{ route('catalog.index', ['stock' => 'available']) }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Ver disponibilidad →</a>
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featured as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-600"><x-icon name="cart" class="h-5 w-5" /></span>
                <h3 class="mt-4 text-base font-bold text-slate-900">Cotiza sin compromiso</h3>
                <p class="mt-1 text-sm text-slate-600">Solicita una cotización con precios actualizados y validez de 15 días.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><x-icon name="truck" class="h-5 w-5" /></span>
                <h3 class="mt-4 text-base font-bold text-slate-900">Delivery y recojo</h3>
                <p class="mt-1 text-sm text-slate-600">Coordina la entrega en obra o recoge tu pedido en tienda.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><x-icon name="users" class="h-5 w-5" /></span>
                <h3 class="mt-4 text-base font-bold text-slate-900">Precios por volumen</h3>
                <p class="mt-1 text-sm text-slate-600">Clientes mayoristas acceden a descuentos automáticos por cantidad.</p>
            </div>
        </div>
    </section>
@endsection
