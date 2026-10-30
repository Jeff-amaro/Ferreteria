@extends('layouts.app')

@section('title', 'Compras a proveedor')

@section('content')
    <x-page-header title="Compras a proveedor" subtitle="Gestiona pedidos, recepciones y retrasos de proveedores.">
        <a href="{{ route('replenishment.index') }}" class="btn-secondary"><x-icon name="refresh" class="h-4 w-4" /> Reposición</a>
        <a href="{{ route('purchases.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Nueva orden de compra</a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="grid gap-3 md:grid-cols-5">
                <div class="relative md:col-span-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Código o proveedor..." class="input pl-9">
                </div>
                <select name="status" class="input">
                    <option value="">Todos los estados</option>
                    @foreach (\App\Models\PurchaseOrder::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="supplier" class="input">
                    <option value="">Todos los proveedores</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected((string) request('supplier') === (string) $supplier->id)>{{ $supplier->business_name }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['search', 'status', 'supplier']))
                        <a href="{{ route('purchases.index') }}" class="btn-ghost">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        @if ($orders->isEmpty())
            <x-empty-state title="No hay órdenes de compra" description="Registra un pedido a proveedor para abastecer el inventario." icon="truck">
                <a href="{{ route('purchases.create') }}" class="btn-primary">Nueva orden de compra</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Proveedor</th>
                            <th>Pedido</th>
                            <th>Entrega esperada</th>
                            <th>Ítems</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr class="{{ $order->is_delayed ? 'bg-rose-50/50' : '' }}">
                                <td>
                                    <a href="{{ route('purchases.show', $order) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $order->code }}</a>
                                    @if ($order->is_delayed)
                                        <p class="text-xs font-semibold text-rose-600">Retrasada {{ $order->days_late }} días</p>
                                    @endif
                                </td>
                                <td class="text-sm text-slate-700">{{ $order->supplier?->business_name }}</td>
                                <td class="text-sm text-slate-600">{{ $order->ordered_at?->format('d/m/Y') }}</td>
                                <td class="text-sm text-slate-600">{{ $order->expected_at?->format('d/m/Y') }}</td>
                                <td class="text-sm text-slate-600">{{ $order->items_count }}</td>
                                <td class="font-semibold text-slate-900">S/ {{ number_format((float) $order->total, 2) }}</td>
                                <td><x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('purchases.show', $order) }}" class="btn-ghost px-2.5 py-2" title="Ver"><x-icon name="eye" class="h-4 w-4" /></a>
                                        @if ($order->status !== 'recibida')
                                            <a href="{{ route('purchases.show', $order) }}" class="btn-ghost px-2.5 py-2 text-emerald-600" title="Verificar recepción"><x-icon name="truck" class="h-4 w-4" /></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 p-4">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
