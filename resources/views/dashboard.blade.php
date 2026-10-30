@extends('layouts.app')

@section('title', 'Panel de control')

@section('content')
    @php($user = auth()->user())

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-primary-600">Hola, {{ explode(' ', $user->name)[0] }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Resumen de tu ferretería</h1>
            <p class="mt-1 text-sm text-slate-500">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }} · {{ $user->role->label() }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($user->hasRole(['administrador', 'vendedor']))
                <a href="{{ route('pos.index') }}" class="btn-primary"><x-icon name="cart" class="h-4 w-4" /> Nuevo punto de venta</a>
                <a href="{{ route('quotations.index') }}" class="btn-secondary">Cotizaciones</a>
            @endif
            @if ($user->hasRole(['administrador', 'almacenero']))
                <a href="{{ route('inventory.create') }}" class="btn-secondary"><x-icon name="clipboard" class="h-4 w-4" /> Nuevo conteo</a>
            @endif
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Ventas de hoy" :value="'S/ '.number_format($stats['sales_today'], 2)" hint="Órdenes no anuladas" icon="wallet" />
        <x-stat-card label="Ventas del mes" :value="'S/ '.number_format($stats['sales_month'], 2)" hint="Acumulado mensual" icon="chart" color="bg-emerald-50 text-emerald-600" />
        <x-stat-card label="Órdenes pendientes" :value="$stats['orders_pending']" hint="En proceso de atención" icon="receipt" color="bg-amber-50 text-amber-600" />
        <x-stat-card label="Alertas pendientes" :value="$stats['alerts']" hint="Inventario y proveedores" icon="alert" color="bg-rose-50 text-rose-600" />
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Productos activos" :value="$stats['products']" hint="Catálogo vigente" icon="box" color="bg-sky-50 text-sky-600" />
        <x-stat-card label="Stock bajo" :value="$stats['low_stock']" hint="Requieren reposición" icon="refresh" color="bg-amber-50 text-amber-600" />
        <x-stat-card label="Clientes activos" :value="$stats['clients']" hint="Cartera registrada" icon="users" color="bg-indigo-50 text-indigo-600" />
        <x-stat-card label="Cotizaciones abiertas" :value="$stats['quotations']" hint="Borrador o emitidas" icon="document" color="bg-slate-100 text-slate-600" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="card p-6 xl:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Ventas de los últimos 6 meses</h2>
                    <p class="text-sm text-slate-500">Totales registrados en órdenes de venta.</p>
                </div>
                <span class="badge bg-primary-50 text-primary-700">S/ {{ number_format($monthlySales->sum('total'), 2) }}</span>
            </div>
            <div class="mt-8 flex h-56 items-end gap-3 sm:gap-5">
                @foreach ($monthlySales as $month)
                    @php($height = max(6, (int) round(($month['total'] / $maxMonthly) * 100)))
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <span class="text-[10px] font-semibold text-slate-500">S/ {{ number_format($month['total'], 0) }}</span>
                        <div class="flex w-full items-end justify-center" style="height: 160px;">
                            <div class="w-full max-w-14 rounded-t-lg bg-primary-600/90 transition hover:bg-primary-700" style="height: {{ $height }}%"></div>
                        </div>
                        <span class="text-xs font-medium capitalize text-slate-500">{{ $month['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900">Más vendidos</h2>
                <span class="text-xs text-slate-400">30 días</span>
            </div>
            <div class="mt-5 space-y-4">
                @forelse ($topProducts as $index => $product)
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500">{{ $index + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $product->product_name }}</p>
                            <p class="text-xs text-slate-500">{{ $product->sku }} · {{ $product->total_quantity }} und.</p>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">S/ {{ number_format((float) $product->total_amount, 2) }}</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-slate-500">Aún no hay ventas registradas.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="card overflow-hidden xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-bold text-slate-900">Últimas órdenes de venta</h2>
                <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Ver todas</a>
            </div>
            @if ($recentOrders->isEmpty())
                <x-empty-state title="Sin órdenes" description="Las órdenes que registres aparecerán aquí." icon="receipt" />
            @else
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Pago</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="font-semibold text-primary-600 hover:text-primary-700">{{ $order->code }}</a>
                                        <p class="text-xs text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td>{{ $order->client->name }}</td>
                                    <td><x-status-badge :color="$order->status_color">{{ $order->status_label }}</x-status-badge></td>
                                    <td><x-status-badge :color="$order->payment_status_color">{{ $order->payment_status_label }}</x-status-badge></td>
                                    <td class="text-right font-semibold">S/ {{ number_format((float) $order->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-bold text-slate-900">Stock crítico</h2>
                <a href="{{ route('replenishment.index') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Reponer</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($lowStockProducts as $product)
                    <div class="flex items-center gap-3 px-6 py-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <x-icon name="box" class="h-4 w-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $product->name }}</p>
                            <p class="text-xs text-slate-500">{{ $product->sku }} · mínimo {{ $product->min_stock }}</p>
                        </div>
                        <span class="badge {{ $product->stock_color }}">{{ $product->stock }} und.</span>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-slate-500">Todo el stock está en orden.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
