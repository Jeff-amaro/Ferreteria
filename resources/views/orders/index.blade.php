@extends('layouts.app')

@section('title', 'Órdenes de venta')

@section('content')
    <x-page-header title="Órdenes de venta" subtitle="Seguimiento de pedidos, pagos, despachos y entregas.">
        <a href="{{ route('pos.index') }}" class="btn-primary"><x-icon name="cart" class="h-4 w-4" /> Nueva venta</a>
    </x-page-header>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-100 p-4">
            <form method="GET" class="grid gap-3 md:grid-cols-5">
                <div class="relative md:col-span-2">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Código, cliente o documento..." class="input pl-9">
                </div>
                <select name="status" class="input">
                    <option value="">Todos los estados</option>
                    @foreach (\App\Models\SalesOrder::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="payment_status" class="input">
                    <option value="">Todos los pagos</option>
                    @foreach (\App\Models\SalesOrder::PAYMENT_STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(request('payment_status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['search', 'status', 'payment_status']))
                        <a href="{{ route('orders.index') }}" class="btn-ghost">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        @if ($orders->isEmpty())
            <x-empty-state title="No hay órdenes de venta" description="Convierte una cotización en orden para iniciar el seguimiento." icon="receipt">
                <a href="{{ route('quotations.index') }}" class="btn-primary">Ver cotizaciones</a>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Pago</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="font-semibold text-slate-800 hover:text-primary-600">{{ $order->code }}</a>
                                    <p class="text-xs text-slate-500">{{ $order->items()->count() }} ítems</p>
                                </td>
                                <td>
                                    <p class="text-sm font-medium text-slate-700">{{ $order->client?->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->client?->document_label }}</p>
                                </td>
                                <td class="text-sm text-slate-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-semibold text-slate-900">S/ {{ number_format((float) $order->total, 2) }}</td>
                                <td><x-status-badge :color="$order->payment_status_color">{{ $order->payment_status_label }}</x-status-badge></td>
                                <td><x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('orders.show', $order) }}" class="btn-ghost px-2.5 py-2" title="Ver"><x-icon name="eye" class="h-4 w-4" /></a>
                                        @if ($order->receipt)
                                            <a href="{{ route('receipts.show', $order->receipt) }}" class="btn-ghost px-2.5 py-2" title="Comprobante"><x-icon name="receipt" class="h-4 w-4" /></a>
                                        @elseif ($order->payment_status !== 'pagado' && $order->status !== 'anulado')
                                            <a href="{{ route('orders.payment', $order) }}" class="btn-ghost px-2.5 py-2 text-emerald-600" title="Registrar pago"><x-icon name="wallet" class="h-4 w-4" /></a>
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
