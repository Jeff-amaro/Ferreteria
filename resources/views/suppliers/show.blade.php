@extends('layouts.app')

@section('title', 'Ficha de proveedor')

@section('content')
    <x-page-header title="Ficha de proveedor" subtitle="Productos asociados y órdenes de compra.">
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-primary"><x-icon name="edit" class="h-4 w-4" /> Editar</a>
        <a href="{{ route('suppliers.index') }}" class="btn-secondary">Volver</a>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-100 text-primary-700"><x-icon name="building" class="h-7 w-7" /></span>
                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-slate-900">{{ $supplier->business_name }}</h2>
                    <p class="text-sm text-slate-500">RUC {{ $supplier->ruc }}</p>
                </div>
            </div>
            <dl class="mt-6 space-y-3 text-sm">
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Contacto</dt><dd class="font-medium text-slate-800">{{ $supplier->contact_name ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Teléfono</dt><dd class="font-medium text-slate-800">{{ $supplier->phone ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Correo</dt><dd class="text-right font-medium text-slate-800">{{ $supplier->email ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Dirección</dt><dd class="text-right font-medium text-slate-800">{{ $supplier->address ?: '—' }}</dd></div>
                <div class="flex justify-between gap-3 border-t border-slate-100 pt-3"><dt class="text-slate-500">Plazo</dt><dd><span class="badge bg-sky-100 text-sky-700">{{ $supplier->lead_time_days }} días</span></dd></div>
            </dl>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-bold text-slate-900">Productos asociados</h3></div>
                @if ($supplier->products->isEmpty())
                    <x-empty-state title="Sin productos" description="Asigna productos a este proveedor desde el módulo de productos." icon="box" />
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead><tr><th>Producto</th><th>SKU</th><th>Stock</th><th class="text-right">Precio</th></tr></thead>
                            <tbody>
                                @foreach ($supplier->products as $product)
                                    <tr>
                                        <td><a href="{{ route('products.show', $product) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $product->name }}</a></td>
                                        <td>{{ $product->sku }}</td>
                                        <td><span class="badge {{ $product->stock_color }}">{{ $product->stock }}</span></td>
                                        <td class="text-right font-semibold">S/ {{ number_format((float) $product->price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-4"><h3 class="text-base font-bold text-slate-900">Órdenes de compra</h3></div>
                @if ($supplier->purchaseOrders->isEmpty())
                    <x-empty-state title="Sin compras" description="Genera órdenes de compra desde el módulo de reposición." icon="truck" />
                @else
                    <div class="overflow-x-auto">
                        <table class="table-base">
                            <thead><tr><th>Orden</th><th>Esperada</th><th>Estado</th><th class="text-right">Total</th></tr></thead>
                            <tbody>
                                @foreach ($supplier->purchaseOrders as $order)
                                    <tr>
                                        <td><a href="{{ route('purchases.show', $order) }}" class="font-semibold text-primary-600">{{ $order->code }}</a></td>
                                        <td>{{ $order->expected_at?->format('d/m/Y') }}</td>
                                        <td><x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge></td>
                                        <td class="text-right font-semibold">S/ {{ number_format((float) $order->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
