@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <x-page-header title="Productos" subtitle="Administra el catálogo, precios y niveles de stock.">
        <a href="{{ route('products.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nuevo producto</a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                <div class="relative xl:col-span-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Nombre, SKU o descripción…" class="input pl-9">
                </div>
                <select name="category" class="input">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="brand" class="input">
                    <option value="">Todas las marcas</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((string) request('brand') === (string) $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
                <select name="stock" class="input">
                    <option value="">Todo el stock</option>
                    <option value="low" @selected(request('stock') === 'low')>Stock bajo</option>
                    <option value="out" @selected(request('stock') === 'out')>Sin stock</option>
                </select>
                <select name="status" class="input">
                    <option value="">Todos los estados</option>
                    <option value="active" @selected(request('status') === 'active')>Activos</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                </select>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="trashed" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked(request()->boolean('trashed'))>
                    Ver dados de baja
                </label>
                <div class="flex gap-2 xl:col-span-2">
                    <button type="submit" class="btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['search', 'category', 'brand', 'stock', 'status', 'trashed']))
                        <a href="{{ route('products.index') }}" class="btn-ghost">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        @if ($products->isEmpty())
            <x-empty-state title="No se encontraron productos" description="Ajusta los filtros o registra un nuevo producto." icon="box">
                <a href="{{ route('products.create') }}" class="btn-primary">Registrar producto</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría / Marca</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if ($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-11 w-11 rounded-lg border border-slate-200 object-cover">
                                        @else
                                            <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-slate-400"><x-icon name="box" class="h-5 w-5" /></span>
                                        @endif
                                        <div>
                                            <a href="{{ route('products.show', $product) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $product->name }}</a>
                                            <p class="text-xs text-slate-500">{{ $product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm text-slate-700">{{ $product->category?->name ?? 'Sin categoría' }}</p>
                                    <p class="text-xs text-slate-500">{{ $product->brand?->name ?? 'Sin marca' }}</p>
                                </td>
                                <td class="font-semibold">S/ {{ number_format((float) $product->price, 2) }}</td>
                                <td>
                                    <x-status-badge :color="$product->stock_color">{{ $product->stock }} {{ $product->unit }}</x-status-badge>
                                    <p class="mt-1 text-[11px] text-slate-400">Mín. {{ $product->min_stock }}</p>
                                </td>
                                <td>
                                    @if ($product->trashed())
                                        <x-status-badge color="bg-rose-100 text-rose-700">Dado de baja</x-status-badge>
                                    @else
                                        <x-status-badge :color="$product->active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'">
                                            {{ $product->active ? 'Activo' : 'Inactivo' }}
                                        </x-status-badge>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('products.show', $product) }}" class="btn-ghost px-2.5 py-2" title="Ver"><x-icon name="eye" class="h-4 w-4" /></a>
                                        @if ($product->trashed())
                                            <form method="POST" action="{{ route('products.restore', $product->id) }}">
                                                @csrf
                                                <button type="submit" class="btn-ghost px-2.5 py-2 text-emerald-600 hover:bg-emerald-50" title="Restaurar"><x-icon name="refresh" class="h-4 w-4" /></button>
                                            </form>
                                        @else
                                            <a href="{{ route('products.edit', $product) }}" class="btn-ghost px-2.5 py-2" title="Editar"><x-icon name="edit" class="h-4 w-4" /></a>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Dar de baja este producto?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-ghost px-2.5 py-2 text-rose-600 hover:bg-rose-50" title="Dar de baja"><x-icon name="trash" class="h-4 w-4" /></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">{{ $products->links() }}</div>
        @endif
    </div>
@endsection
