@extends('layouts.public')

@section('title', 'Catálogo')

@section('content')
    <section class="border-b border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-primary-600">Catálogo EJJ</p>
            <h1 class="mt-1 text-3xl font-bold text-slate-900">Encuentra lo que necesitas para tu proyecto</h1>
            <p class="mt-2 max-w-2xl text-sm text-slate-600">Herramientas, materiales, plomería, electricidad y más. Precios en soles e IGV calculado en tu cotización.</p>

            <form method="GET" class="mt-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-5">
                <div class="relative md:col-span-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar producto, SKU o marca..." class="input pl-9">
                </div>
                <select name="category" class="input">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="sort" class="input">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Más recientes</option>
                    <option value="name" @selected(request('sort') === 'name')>Nombre A-Z</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Menor precio</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Mayor precio</option>
                </select>
                <button type="submit" class="btn-primary">Buscar</button>
                <label class="flex items-center gap-2 text-sm text-slate-600 md:col-span-5">
                    <input type="checkbox" name="stock" value="available" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked(request('stock') === 'available')>
                    Mostrar solo productos con stock disponible
                </label>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-600">{{ $products->total() }} productos encontrados</p>
            @if (request()->hasAny(['search', 'category', 'sort', 'stock']))
                <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-primary-600 hover:underline">Limpiar filtros</a>
            @endif
        </div>

        @if ($products->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center">
                <x-icon name="search" class="mx-auto h-10 w-10 text-slate-300" />
                <p class="mt-3 text-lg font-semibold text-slate-800">No encontramos productos</p>
                <p class="mt-1 text-sm text-slate-500">Prueba con otro término o revisa todas las categorías.</p>
                <a href="{{ route('catalog.index') }}" class="btn-primary mt-5">Ver todo el catálogo</a>
            </div>
        @else
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div class="mt-8">{{ $products->links() }}</div>
        @endif
    </section>
@endsection
