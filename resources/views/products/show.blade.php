@extends('layouts.app')

@section('title', 'Detalle de producto')

@section('content')
    <x-page-header title="Detalle de producto" subtitle="Ficha técnica, disponibilidad e historial de ventas.">
        <a href="{{ route('products.edit', $product) }}" class="btn-primary"><x-icon name="edit" class="h-4 w-4" /> Editar</a>
        <a href="{{ route('products.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-6 lg:col-span-2">
            <div class="flex flex-col gap-6 sm:flex-row">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-40 w-40 shrink-0 rounded-2xl border border-slate-200 object-cover">
                @else
                    <div class="flex h-40 w-40 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                        <x-icon name="box" class="h-12 w-12" />
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :color="$product->stock_color">{{ $product->stock_status }}</x-status-badge>
                        <x-status-badge :color="$product->active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'">{{ $product->active ? 'Activo' : 'Inactivo' }}</x-status-badge>
                    </div>
                    <h2 class="mt-3 text-2xl font-bold text-slate-900">{{ $product->name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">SKU {{ $product->sku }} · {{ $product->category?->name ?? 'Sin categoría' }} · {{ $product->brand?->name ?? 'Sin marca' }}</p>
                    <p class="mt-4 text-3xl font-bold text-primary-600">S/ {{ number_format((float) $product->price, 2) }}</p>
                    <p class="mt-1 text-xs text-slate-500">Costo: S/ {{ number_format((float) $product->cost, 2) }} · Unidad: {{ $product->unit }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 border-t border-slate-100 pt-6 sm:grid-cols-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Descripción</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $product->description ?: 'Sin descripción registrada.' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Especificaciones técnicas</h3>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $product->specs ?: 'Sin especificaciones registradas.' }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-6">
                <h3 class="text-sm font-bold text-slate-900">Disponibilidad</h3>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">Stock actual</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $product->stock }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">Stock mínimo</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $product->min_stock }}</p>
                    </div>
                </div>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Proveedor</span><span class="font-medium text-slate-800">{{ $product->supplier?->business_name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Plazo de entrega</span><span class="font-medium text-slate-800">{{ $product->supplier?->lead_time_days ?? '—' }} días</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Registrado</span><span class="font-medium text-slate-800">{{ $product->created_at->format('d/m/Y') }}</span></div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-sm font-bold text-slate-900">Consultar stock rápido</h3>
                <p class="mt-1 text-xs text-slate-500">Verificación inmediata de inventario (CUS 06).</p>
                <div class="mt-3 flex items-center justify-between rounded-xl bg-primary-50 px-4 py-3">
                    <span class="text-sm font-medium text-primary-700">Disponible para venta</span>
                    <span class="text-lg font-bold text-primary-700">{{ $product->stock }} {{ $product->unit }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-bold text-slate-900">Últimos movimientos de venta</h3>
        </div>
        @if ($movements->isEmpty())
            <x-empty-state title="Sin movimientos" description="Este producto aún no registra ventas." icon="receipt" />
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr><th>Orden</th><th>Fecha</th><th>Cantidad</th><th>Precio unitario</th><th class="text-right">Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($movements as $movement)
                            <tr>
                                <td><a href="{{ route('orders.show', $movement->salesOrder) }}" class="font-semibold text-primary-600">{{ $movement->salesOrder?->code }}</a></td>
                                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $movement->quantity }}</td>
                                <td>S/ {{ number_format((float) $movement->unit_price, 2) }}</td>
                                <td class="text-right font-semibold">S/ {{ number_format((float) $movement->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
